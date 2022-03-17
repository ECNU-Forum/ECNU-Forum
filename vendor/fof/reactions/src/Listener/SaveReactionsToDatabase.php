<?php

/*
 * This file is part of fof/reactions.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Reactions\Listener;

use Flarum\Extension\ExtensionManager;
use Flarum\Foundation\ValidationException;
use Flarum\Likes\Event\PostWasLiked;
use Flarum\Post\Event\Saving;
use Flarum\Post\Post;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use FoF\Gamification\Listeners\SaveVotesToDatabase;
use FoF\Reactions\Event\PostWasReacted;
use FoF\Reactions\Event\PostWasUnreacted;
use FoF\Reactions\PostReaction;
use FoF\Reactions\Reaction;
use Illuminate\Support\Arr;
use Pusher;
use Symfony\Contracts\Translation\TranslatorInterface;

class SaveReactionsToDatabase
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ExtensionManager
     */
    protected $extensions;

    public function __construct(SettingsRepositoryInterface $settings, TranslatorInterface $translator, ExtensionManager $extensions)
    {
        $this->settings = $settings;
        $this->translator = $translator;
        $this->extensions = $extensions;
    }

    /**
     * @param Saving $event
     *
     * @throws \Flarum\User\Exception\PermissionDeniedException
     * @throws \Flarum\Foundation\ValidationException
     */
    public function handle(Saving $event)
    {
        $post = $event->post;
        $data = $event->data;

        if ($post->exists && Arr::has($data, 'attributes.reaction')) {
            $actor = $event->actor;

            $reactionId = Arr::get($data, 'attributes.reaction');
            $reactionIdentifier = null;

            $actor->assertCan('react', $post);

            if ($actor->id === $post->user_id) {
                throw new ValidationException([
                    'message' => $this->translator->trans('fof-reactions.forum.reacting-own-post'),
                ]);
            }

            $reaction = !is_null($reactionId) ? Reaction::where('id', $reactionId)->first() : null;

            $gamification = $this->extensions->isEnabled('fof-gamification');
            $likes = $this->extensions->isEnabled('flarum-likes');

            $gamificationUpvote = $this->settings->get('fof-reactions.convertToUpvote');
            $gamificationDownvote = $this->settings->get('fof-reactions.convertToDownvote');

            if ($gamification && class_exists(SaveVotesToDatabase::class) && $reaction && $reaction->identifier == $gamificationUpvote) {
                resolve(SaveVotesToDatabase::class)->vote(
                    $post,
                    false,
                    true,
                    $actor,
                    $post->user
                );
            } elseif ($gamification && class_exists(SaveVotesToDatabase::class) && $reaction && $reaction->identifier == $gamificationDownvote) {
                resolve(SaveVotesToDatabase::class)->vote(
                    $post,
                    true,
                    false,
                    $actor,
                    $post->user
                );
            } elseif ($likes && $reaction && $reaction->identifier == $this->settings->get('fof-reactions.convertToLike')) {
                $liked = $post->likes()->where('user_id', $actor->id)->exists();

                if ($liked) {
                    return;
                } else {
                    $post->likes()->attach($actor->id);

                    $post->raise(new PostWasLiked($post, $actor));
                }
            } else {
                $postReaction = PostReaction::where([['user_id', $actor->id], ['post_id', $post->id]])->first();
                $removeReaction = is_null($reactionId) || ($postReaction && $postReaction->reaction_id == $reactionId);

                if ($removeReaction) {
                    if ($postReaction) {
                        $this->push('removedReaction', $postReaction, $reaction ?: $postReaction->reaction, $actor, $post);

                        $postReaction->reaction_id = null;
                        $postReaction->save();
                    }

                    $post->raise(new PostWasUnreacted($post, $actor));
                } else {
                    $this->validateReaction($reactionId);

                    if ($postReaction) {
                        $postReaction->reaction_id = $reaction->id;
                        $postReaction->save();
                    } else {
                        $postReaction = new PostReaction();

                        $postReaction->post_id = $post->id;
                        $postReaction->user_id = $actor->id;
                        $postReaction->reaction_id = $reaction->id;

                        $postReaction->save();
                    }

                    $this->push('newReaction', $postReaction, $reaction, $actor, $post);

                    $post->raise(new PostWasReacted($post, $actor, $reaction));
                }
            }
        }
    }

    /**
     * @param $event
     * @param PostReaction $postReaction
     * @param Reaction     $reaction
     * @param User         $actor
     * @param Post         $post
     */
    public function push($event, PostReaction $postReaction, Reaction $reaction, User $actor, Post $post)
    {
        if ($pusher = $this->getPusher()) {
            $pusher->trigger('public', $event, [
                'id'         => (string) $postReaction->id,
                'reactionId' => $reaction->id,
                'postId'     => $post->id,
                'userId'     => $actor->id,
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

        if (resolve('container')->bound(Pusher::class)) {
            return resolve(Pusher::class);
        } else {
            $settings = resolve('flarum.settings');

            $options = [];

            if ($cluster = $settings->get('flarum-pusher.app_cluster')) {
                $options['cluster'] = $cluster;
            }

            return new Pusher(
                $settings->get('flarum-pusher.app_key'),
                $settings->get('flarum-pusher.app_secret'),
                $settings->get('flarum-pusher.app_id'),
                $options
            );
        }
    }

    protected function validateReaction($reactionId)
    {
        if (is_null($reactionId)) {
            return;
        }

        $reaction = Reaction::find($reactionId);

        if (!$reaction || !$reaction->enabled) {
            throw new ValidationException([
                'message' => $this->translator->trans('fof-reactions.forum.disabled-reaction'),
            ]);
        }
    }
}
