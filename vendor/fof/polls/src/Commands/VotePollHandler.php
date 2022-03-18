<?php

/*
 * This file is part of fof/polls.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Polls\Commands;

use Flarum\Settings\SettingsRepositoryInterface;
use FoF\Polls\Events\PollWasVoted;
use FoF\Polls\Poll;
use FoF\Polls\PollOption;
use FoF\Polls\PollVote;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Pusher;

class VotePollHandler
{
    /**
     * @var Dispatcher
     */
    private $events;

    /**
     * @var SettingsRepositoryInterface
     */
    private $settings;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param Dispatcher                  $events
     * @param SettingsRepositoryInterface $settings
     * @param Container                   $container
     */
    public function __construct(Dispatcher $events, SettingsRepositoryInterface $settings, Container $container)
    {
        $this->events = $events;
        $this->settings = $settings;
        $this->container = $container;
    }

    public function handle(VotePoll $command)
    {
        $actor = $command->actor;
        /**
         * @var $poll Poll
         */
        $poll = Poll::findOrFail($command->pollId);
        $data = $command->data;

        $optionId = Arr::get($data, 'optionId');

        $actor->assertCan('vote', $poll);

        /**
         * @var $vote PollVote|null
         */
        $vote = $poll->votes()->where('user_id', $actor->id)->first();

        if ($vote) {
            $actor->assertCan('changeVote', $poll);
        }

        $previousOption = null;

        if ($optionId === null && $vote !== null) {
            $previousOption = $vote->option;

            $vote->delete();
            $vote = null;
        } elseif ($optionId !== null) {
            if ($vote) {
                $previousOption = $vote->option;

                $vote->option_id = $optionId;
                $vote->save();
            } else {
                $vote = $poll->votes()->create([
                    'user_id'   => $actor->id,
                    'option_id' => $optionId,
                ]);
            }

            // Forget the relation in case is was loaded for $previousOption
            $vote->unsetRelation('option');

            $vote->option->refreshVoteCount()->save();

            $this->events->dispatch(new PollWasVoted($actor, $poll, $vote, $vote !== null));

            $this->pushNewVote($vote);
        }

        $poll->refreshVoteCount()->save();

        if ($previousOption) {
            $previousOption->refreshVoteCount()->save();

            $this->pushUpdatedOption($previousOption);
        }

        if ($vote) {
            $this->pushUpdatedOption($vote->option);
        }

        return $poll;
    }

    /**
     * Pushes a new vote through websocket. Kept for backward compatibility, but we are no longer using it.
     *
     * @param PollVote $vote
     *
     * @throws \Pusher\PusherException
     */
    public function pushNewVote($vote)
    {
        if ($pusher = $this->getPusher()) {
            $pusher->trigger('public', 'newPollVote', $vote);
        }
    }

    /**
     * Pushes an updated option through websocket.
     *
     * @param PollOption $option
     *
     * @throws \Pusher\PusherException
     */
    public function pushUpdatedOption(PollOption $option)
    {
        if ($pusher = $this->getPusher()) {
            $pusher->trigger('public', 'updatedPollOption', [
                'pollId'          => $option->poll->id,
                'pollVoteCount'   => $option->poll->vote_count,
                'optionId'        => $option->id,
                'optionVoteCount' => $option->vote_count,
            ]);
        }
    }

    /**
     * @throws \Pusher\PusherException
     *
     * @return bool|\Illuminate\Foundation\Application|mixed|Pusher
     */
    private function getPusher()
    {
        if (!class_exists(Pusher::class)) {
            return false;
        }

        if ($this->container->bound(Pusher::class)) {
            return $this->container->make(Pusher::class);
        } else {
            $options = [];

            if ($cluster = $this->settings->get('flarum-pusher.app_cluster')) {
                $options['cluster'] = $cluster;
            }

            return new Pusher(
                $this->settings->get('flarum-pusher.app_key'),
                $this->settings->get('flarum-pusher.app_secret'),
                $this->settings->get('flarum-pusher.app_id'),
                $options
            );
        }
    }
}
