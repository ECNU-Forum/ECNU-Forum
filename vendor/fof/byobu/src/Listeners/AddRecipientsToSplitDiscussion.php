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

use Fof\Split\Events\DiscussionWasSplit;

class AddRecipientsToSplitDiscussion
{
    /**
     * @param DiscussionWasSplit $event
     */
    public function handle(DiscussionWasSplit $event)
    {
        $event->newDiscussion->recipientUsers()->sync(
            $event->originalDiscussion->recipientUsers()->allRelatedIds()->all()
        );
        $event->newDiscussion->recipientGroups()->sync(
            $event->originalDiscussion->recipientGroups()->allRelatedIds()->all()
        );
    }
}
