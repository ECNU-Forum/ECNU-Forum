<?php

/*
 * This file is part of fof/merge-discussions.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\MergeDiscussions\Posts;

use Flarum\Post\AbstractEventPost;
use Flarum\Post\MergeableInterface;
use Flarum\Post\Post;
use Illuminate\Support\Collection;

class DiscussionMergePost extends AbstractEventPost implements MergeableInterface
{
    /**
     * {@inheritdoc}
     */
    public static $type = 'discussionMerged';

    /**
     * Save the model, given that it is going to appear immediately after the
     * passed model.
     *
     * @param Post|null $previous
     *
     * @return Post The model resulting after the merge. If the merge is
     *              unsuccessful, this should be the current model instance. Otherwise,
     *              it should be the model that was merged into.
     */
    public function saveAfter(Post $previous = null)
    {
        $this->save();

        return $this;
    }

    public static function reply(int $discussionId, int $userId, int $postsCount, Collection $mergedDiscussions): self
    {
        $post = new static();

        $post->content = static::buildContent($postsCount, $mergedDiscussions);
        $post->created_at = time();
        $post->discussion_id = $discussionId;
        $post->user_id = $userId;

        return $post;
    }

    public static function buildContent(int $postsCount, Collection $discussions): array
    {
        return [
            'count'  => (int) $postsCount,
            'titles' => $discussions->map(function ($d) {
                return $d->title;
            }),
        ];
    }
}
