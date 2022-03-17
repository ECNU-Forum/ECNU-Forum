<?php

/*
 * This file is part of fof/links.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Links\Api\Controller;

use FoF\Links\Link;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OrderLinksController implements RequestHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $request->getAttribute('actor')->assertAdmin();
        $order = Arr::get($request->getParsedBody(), 'order');

        if ($order === null) {
            return new EmptyResponse(422);
        }

        resolve('db.connection')->transaction(function () use ($order) {
            // code adapted from flarum/tags
            // https://github.com/flarum/tags/blob/a0744cf9d91819f7628bef1ac27ecb96c6ee97f1/src/Api/Controller/OrderTagsController.php

            Link::query()->update([
                'position'  => null,
                'parent_id' => null,
            ]);

            foreach ($order as $i => $parent) {
                $parentId = Arr::get($parent, 'id');

                Link::query()->where('id', $parentId)->update(['position' => $i]);

                if (isset($parent['children']) && is_array($parent['children'])) {
                    foreach ($parent['children'] as $j => $childId) {
                        Link::query()->where('id', $childId)->update([
                            'position'  => $j,
                            'parent_id' => $parentId,
                        ]);
                    }
                }
            }
        });

        return new EmptyResponse(204);
    }
}
