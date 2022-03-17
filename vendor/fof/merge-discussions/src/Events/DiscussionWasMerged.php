<?php

/*
 * This file is part of fof/merge-discussions.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\MergeDiscussions\Events;

use Flarum\Discussion\Discussion;
use Flarum\Post\Post;
use Flarum\User\User;
use Illuminate\Support\Collection;

class DiscussionWasMerged
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var Post[]|Collection
     */
    public $posts;

    /**
     * @var Discussion
     */
    public $discussion;

    /**
     * @var Discussion[]|Collection Discussion
     */
    public $mergedDiscussions;

    public function __construct(User $actor, Collection $posts, Discussion $discussion, Collection $mergedDiscussions)
    {
        $this->actor = $actor;
        $this->posts = $posts;
        $this->discussion = $discussion;
        $this->mergedDiscussions = $mergedDiscussions;
    }
}
