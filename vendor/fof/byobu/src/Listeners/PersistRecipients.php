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

use Carbon\Carbon;
use Flarum\Discussion\Discussion;
use Flarum\Discussion\Event\Saving;
use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\User;
use FoF\Byobu\Concerns\ExtensionsDiscovery;
use FoF\Byobu\Discussion\Screener;
use FoF\Byobu\Events;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PersistRecipients
{
    use ExtensionsDiscovery;

    /**
     * @var Screener
     */
    protected $screener;

    public function handle(Saving $event)
    {
        if (!$this->eventSubmitsRelationships($event->data)) {
            return;
        }

        /** @var Screener $screener */
        $screener = resolve('byobu.screener');
        $this->screener = $screener->whenSavingDiscussions($event);

        if ($this->screener->nothingChanged()) {
            return null;
        }

        if ($event->actor->cannot('startPrivateDiscussionWithBlockers') && $this->screener->hasBlockingUsers()) {
            throw new PermissionDeniedException('Not allowed to add users that blocked receiving private discussions');
        }

        if ($event->actor->cannot('transformToPublic', $event->discussion) && $this->screener->makingPublic()) {
            throw new PermissionDeniedException('Not allowed to convert to a public discussion');
        }

        if (!$event->discussion->exists) {
            $this->checkPermissionsForNewDiscussion($event->actor);
            $event->discussion->isByobu = true;
        } else {
            $this->checkPermissionsForExistingDiscussion($event->actor, $event->discussion);
        }

        // When discussions need approval and this is a private disucussion, ignore approvals.
        if ($this->screener->isPrivate() && $this->extensionIsEnabled('flarum-approval')) {
            $event->discussion->is_approved = true;
        }

        // Private discussions that used to be private but no longer have any recipients
        // now by default will be soft deleted/hidden.
        // The Deleting event is dispatched, if a listener interferes by returning
        // a non-null response the discussion will not be soft deleted.
        if ($this->screener->wasPrivate() && !$this->screener->isPrivate() && !$this->screener->makingPublic()) {
            /** @var Dispatcher $events */
            $events = resolve(Dispatcher::class);

            $eventArgs = $this->eventArguments($event->discussion);

            if ($events->until(new Events\Deleting(...$eventArgs)) === null) {
                $event->discussion->hide($event->actor);
            }
        }

        $this->raiseEvent($event->discussion);

        Discussion::saving(function (Discussion $discussion) {
            $discussion->offsetUnset('isByobu');
        });

        $event->discussion->afterSave(function (Discussion $discussion) {
            foreach (['users', 'groups'] as $type) {
                $relation = 'recipient'.Str::ucfirst($type);

                // Add models that weren't stored yet.
                $discussion->{$relation}()->saveMany(
                    $this->screener->added($type),
                    ['removed_at' => null]
                );

                $this->screener->deleted($type)->each(function ($model) use ($discussion, $relation) {
                    $discussion->{$relation}()->updateExistingPivot(
                        $model,
                        ['removed_at' => Carbon::now()]
                    );
                });
            }
        });
    }

    protected function eventArguments(Discussion $discussion): array
    {
        return [
            $discussion,
            $this->screener,
        ];
    }

    protected function raiseEvent(Discussion $discussion)
    {
        $args = $this->eventArguments($discussion);

        if ($this->screener->isPrivate() && !$discussion->exists) {
            $event = new Events\Created(...$args);
        } elseif ($this->screener->makingPublic()) {
            $event = new Events\DiscussionMadePublic(...$args);
        } elseif ($this->screener->actorRemoved()) {
            $event = new Events\RemovedSelf(...$args);
        } else {
            $event = new Events\RecipientsChanged(...$args);
        }

        $discussion->raise($event);
    }

    protected function checkPermissionsForNewDiscussion(User $user)
    {
        if ($this->screener->users->isNotEmpty() && $user->cannot('discussion.startPrivateDiscussionWithUsers')) {
            throw new PermissionDeniedException('Not allowed to add users to a private discussion');
        }
        if ($this->screener->groups->isNotEmpty() && $user->cannot('discussion.startPrivateDiscussionWithGroups')) {
            throw new PermissionDeniedException('Not allowed to add groups to a private discussion');
        }
    }

    protected function checkPermissionsForExistingDiscussion(User $user, Discussion $discussion)
    {
        // Actor should always be able to remove themself.
        if ($this->screener->onlyActorRemoved()) {
            return;
        }

        if ($this->screener->users->isNotEmpty() && $user->cannot('discussion.editUserRecipients', $discussion)) {
            throw new PermissionDeniedException('Not allowed to change users in a private discussion');
        }
        if ($this->screener->groups->isNotEmpty() && $user->cannot('discussion.editGroupRecipients')) {
            throw new PermissionDeniedException('Not allowed to change groups in a private discussion');
        }
    }

    protected function eventSubmitsRelationships(array $data): bool
    {
        $valid = false;

        foreach (['users', 'groups'] as $type) {
            $relation = Screener::relationName($type);

            $valid = $valid || Arr::has($data, "relationships.$relation");
        }

        return $valid;
    }
}
