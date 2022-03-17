<?php

/*
 * This file is part of fof/merge-discussions.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\MergeDiscussions\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Discussion\Discussion;
use FoF\MergeDiscussions\Api\Commands\MergeDiscussion;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class MergePreviewController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = DiscussionSerializer::class;

    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * {@inheritdoc}
     */
    public $include = [
        'posts',
        'posts.discussion',
        'posts.user',
        'posts.user.groups',
        'posts.editedUser',
        'posts.hiddenUser',
    ];

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    /**
     * Get the data to be serialized and assigned to the response document.
     *
     * @param ServerRequestInterface $request
     * @param Document               $document
     *
     * @return mixed
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        $discussion = Arr::get($request->getQueryParams(), 'id');
        $ids = Arr::get($request->getQueryParams(), 'ids');
        $ordering = Arr::get($request->getQueryParams(), 'ordering');

        /**
         * @var Discussion
         */
        $discussion = $this->bus->dispatch(
            new MergeDiscussion($actor, $discussion, $ids, $ordering, false)
        );

        $discussion->setRelation('posts', $discussion->posts);

        return $discussion;
    }
}
