<?php

/*
 * This file is part of fof/links.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Links;

use Flarum\Api\Controller\ShowForumController;
use Flarum\Foundation\Config;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

class LoadForumLinksRelationship
{
    /** @var Config */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param ShowForumController $controller
     * @param $data
     * @param ServerRequestInterface $request
     */
    public function __invoke(ShowForumController $controller, &$data, ServerRequestInterface $request)
    {
        /** @var \Flarum\User\User */
        $actor = $request->getattribute('actor');
        $adminPath = Arr::get($this->config, 'paths.admin');

        // So that admins don't have to see guest only items but can manage them in admin panel,
        // we only serialize all links if we're visiting the admin panel
        if ($actor->isAdmin() && $request->getServerParams()['REQUEST_URI'] === "/$adminPath") {
            return $data['links'] = Link::all();
        }

        $data['links'] = $actor->isGuest() ?
            Link::where('visibility', 'guests')->orWhere('visibility', 'everyone')->get() :
            Link::where('visibility', 'members')->orWhere('visibility', 'everyone')->get();
    }
}
