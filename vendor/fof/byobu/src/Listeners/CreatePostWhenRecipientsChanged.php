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

use FoF\Byobu\Events\AbstractRecipientsEvent;
use FoF\Byobu\Events\Created;
use FoF\Byobu\Events\DiscussionMadePublic;
use FoF\Byobu\Events\RecipientsChanged;
use FoF\Byobu\Events\RemovedSelf;
use FoF\Byobu\Posts\MadePublic;
use FoF\Byobu\Posts\RecipientLeft;
use FoF\Byobu\Posts\RecipientsModified;
use Illuminate\Contracts\Events\Dispatcher;

class CreatePostWhenRecipientsChanged
{
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(Created::class, [$this, 'whenDiscussionWasTagged']);
        $events->listen(DiscussionMadePublic::class, [$this, 'whenMadePublic']);
        $events->listen(RecipientsChanged::class, [$this, 'whenDiscussionWasTagged']);
        $events->listen(RemovedSelf::class, [$this, 'whenActorRemovedSelf']);
    }

    /**
     * @param AbstractRecipientsEvent $event
     */
    public function whenDiscussionWasTagged(AbstractRecipientsEvent $event)
    {
        $post = RecipientsModified::reply($event);

        $event->discussion->mergePost($post);
    }

    public function whenActorRemovedSelf(RemovedSelf $event)
    {
        $post = RecipientLeft::reply($event);

        $event->discussion->mergePost($post);
    }

    public function whenMadePublic(DiscussionMadePublic $event)
    {
        $post = MadePublic::reply($event);

        $event->discussion->mergePost($post);
    }
}
