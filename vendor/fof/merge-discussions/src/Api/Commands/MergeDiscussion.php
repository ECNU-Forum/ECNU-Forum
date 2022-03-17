<?php

/*
 * This file is part of fof/merge-discussions.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\MergeDiscussions\Api\Commands;

use Flarum\User\User;
use Illuminate\Support\Arr;

class MergeDiscussion
{
    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * Discussion id to merge other discussions into.
     *
     * @var int
     */
    public $discussionId;

    /**
     * The discussion ids to merge.
     *
     * @var int[]
     */
    public $ids;

    /**
     * @var bool Save merged discussion to database
     */
    public $merge;

    /**
     * MergeDiscussion constructor.
     *
     * @param User $actor
     * @param $discussionId
     * @param int[] $ids
     * @param $ordering
     * @param bool $merge
     */
    public function __construct(User $actor, $discussionId, $ids, $ordering = 'date', $merge = true)
    {
        $this->actor = $actor;
        $this->discussionId = (int) $discussionId;
        $this->ids = Arr::wrap($ids);
        $this->ordering = $ordering;
        $this->merge = $merge;
    }
}
