<?php

/*
 * This file is part of fof/byobu.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Byobu\Posts;

use Flarum\Post\AbstractEventPost;
use Flarum\Post\MergeableInterface;
use Flarum\Post\Post;
use FoF\Byobu\Events\AbstractRecipientsEvent;

/**
 * @property array $content
 */
class RecipientsModified extends AbstractEventPost implements MergeableInterface
{
    /**
     * {@inheritdoc}
     */
    public static $type = 'recipientsModified';

    protected $states = ['new', 'old'];
    protected $types = ['users', 'groups'];

    /**
     * @param Post|null|RecipientsModified $previous
     *
     * @return $this|RecipientsModified|Post
     */
    public function saveAfter(Post $previous = null)
    {
        /** @var RecipientsModified $previous */
        if ($previous instanceof static) {
            // .. @todo
        }

        $this->save();

        return $this;
    }

    /**
     * Create a new instance in reply to a discussion.
     *
     * @param AbstractRecipientsEvent $event
     *
     * @return static
     */
    public static function reply(AbstractRecipientsEvent $event)
    {
        $post = new static();

        $post->content = [
            'new' => [
                'users'  => $event->screener->users->pluck('id')->all(),
                'groups' => $event->screener->groups->pluck('id')->all(),
            ],
            'old' => [
                'users'  => $event->screener->currentUsers->pluck('id')->all(),
                'groups' => $event->screener->currentGroups->pluck('id')->all(),
            ],
        ];
        $post->created_at = time();
        $post->discussion_id = $event->discussion->id;
        $post->user_id = $event->actor->id;

        return $post;
    }
}
