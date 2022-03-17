<?php

/*
 * This file is part of fof/merge-discussions.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\MergeDiscussions\Listeners;

use FoF\MergeDiscussions\Events\DiscussionWasMerged;
use FoF\MergeDiscussions\Posts\DiscussionMergePost;

class CreatePostWhenMerged
{
    public function handle(DiscussionWasMerged $event)
    {
        $post = DiscussionMergePost::reply(
            $event->discussion->id,
            $event->actor->id,
            count($event->posts),
            $event->mergedDiscussions
        );

        $event->discussion->mergePost($post);
    }
}
