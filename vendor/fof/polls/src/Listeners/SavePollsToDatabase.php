<?php

/*
 * This file is part of fof/polls.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Polls\Listeners;

use Carbon\Carbon;
use Flarum\Discussion\Event\Saving;
use FoF\Polls\Events\PollWasCreated;
use FoF\Polls\Poll;
use FoF\Polls\PollOption;
use FoF\Polls\Validators\PollOptionValidator;
use FoF\Polls\Validators\PollValidator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

class SavePollsToDatabase
{
    /**
     * @var PollValidator
     */
    protected $validator;

    /**
     * @var PollOptionValidator
     */
    protected $optionValidator;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * SavePollToDatabase constructor.
     *
     * @param Dispatcher          $events
     * @param PollValidator       $validator
     * @param PollOptionValidator $optionValidator
     */
    public function __construct(PollValidator $validator, PollOptionValidator $optionValidator, Dispatcher $events)
    {
        $this->validator = $validator;
        $this->optionValidator = $optionValidator;
        $this->events = $events;
    }

    public function handle(Saving $event)
    {
        if ($event->discussion->exists || !isset($event->data['attributes']['poll'])) {
            return;
        }

        $event->actor->assertCan('startPolls');

        $attributes = $event->data['attributes']['poll'];
        $options = Arr::get($attributes, 'relationships.options', []);

        $this->validator->assertValid($attributes);

        foreach ($options as $option) {
            $this->optionValidator->assertValid(['answer' => $option]);
        }

        $event->discussion->afterSave(function ($discussion) use ($options, $attributes, $event) {
            $endDate = Arr::get($attributes, 'endDate');

            $poll = Poll::build(
                Arr::get($attributes, 'question'),
                $discussion->id,
                $event->actor->id,
                $endDate !== null ? Carbon::createFromTimeString($endDate) : null,
                Arr::get($attributes, 'publicPoll')
            );

            $poll->save();

            $this->events->dispatch(new PollWasCreated($event->actor, $poll));

            foreach ($options as $answer) {
                if (empty($answer)) {
                    continue;
                }

                $option = PollOption::build($answer);

                $poll->options()->save($option);
            }
        });
    }
}
