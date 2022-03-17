<?php

/*
 * This file is part of fof/byobu.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Byobu\Listeners;

use Flarum\Post\Event\Saving;
use FoF\Byobu\Events\Created;
use FoF\Byobu\Events\DiscussionMadePublic;
use FoF\Byobu\Events\RecipientsChanged;
use FoF\Byobu\Events\RemovedSelf;
use FoF\Byobu\Jobs;
use Illuminate\Events\Dispatcher;
use s9e\TextFormatter\Utils;

class QueueNotificationJobs
{
    public function subscribe(Dispatcher $events)
    {
        $events->listen(Created::class, [$this, 'discussionMadePrivate']);
        $events->listen(Saving::class, [$this, 'postMadeInPrivateDiscussion']);
        $events->listen(RemovedSelf::class, [$this, 'discussionRecipientRemovedSelf']);
        $events->listen(RecipientsChanged::class, [$this, 'discussionRecipientsChanged']);
        $events->listen(DiscussionMadePublic::class, [$this, 'discussionMadePublic']);
    }

    public function discussionMadePrivate(Created $event)
    {
        resolve('flarum.queue.connection')->push(
            new Jobs\SendNotificationWhenPrivateDiscussionStarted(
                $event->discussion,
                $event->screener->users,
                $event->screener->groups
            )
        );
    }

    public function postMadeInPrivateDiscussion(Saving $event)
    {
        // stop the notification from firing when events such as flarum/likes or fof/reactions re-save the post.
        if ($event->post->exists) {
            return;
        }

        // If the post content contains a postmention, don't notify here, we will assume some other event will handle it.
        $postMentions = Utils::getAttributeValues($event->post->parsedContent, 'POSTMENTION', 'id');
        if (count($postMentions) > 0) {
            return;
        }

        $actor = $event->actor;

        $event->post->afterSave(function ($post) use ($actor) {
            if ($post->discussion->recipientUsers->count() && $post->number !== 1) {
                resolve('flarum.queue.connection')->push(
                    new Jobs\SendNotificationWhenPostedInPrivateDiscussion($post, $actor)
                );
            }
        });
    }

    public function discussionRecipientRemovedSelf(RemovedSelf $event)
    {
        resolve('flarum.queue.connection')->push(
            new Jobs\SendNotificationWhenRecipientRemoved(
                $event->screener->actor(),
                $event->discussion,
                $event->screener->users
            )
        );
    }

    public function discussionRecipientsChanged(RecipientsChanged $event)
    {
        resolve('flarum.queue.connection')->push(
            new Jobs\SendNotificationWhenRecipientAdded(
                $event->screener->actor(),
                $event->discussion,
                $event->screener->users,
                $event->screener->currentUsers
            )
        );
    }

    public function discussionMadePublic(DiscussionMadePublic $event)
    {
        resolve('flarum.queue.connection')->push(
            new Jobs\SendNotificationWhenDiscussionMadePublic($event->actor, $event->discussion, $event->screener->users)
        );
    }
}
