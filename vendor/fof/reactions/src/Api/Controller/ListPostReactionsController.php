<?php

/*
 * This file is part of fof/reactions.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Reactions\Api\Controller;

use Flarum\Api\Controller\AbstractListController;
use Flarum\Post\PostRepository;
use FoF\Reactions\Api\Serializer\PostReactionSerializer;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListPostReactionsController extends AbstractListController
{
    public $serializer = PostReactionSerializer::class;

    public $include = ['reaction'];

    public $optionalInclude = ['user', 'post'];

    /**
     * @var PostRepository
     */
    protected $posts;

    public function __construct(PostRepository $posts)
    {
        $this->posts = $posts;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Document               $document
     *
     * @return mixed
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $postId = Arr::get($request->getQueryParams(), 'id');
        $post = $this->posts->findOrFail($postId, $request->getAttribute('actor'));

        return $post->reactions()->whereNotNull('reaction_id')->get();
    }
}
