<?php

/*
 * This file is part of fof/links.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Links\Event;

use Flarum\User\User;
use FoF\Links\Link;

abstract class AbstractLinkEvent
{
    /**
     * @var Link
     */
    public $link;

    /**
     * @var User
     */
    public $actor;

    /**
     * @var array
     */
    public $data;

    /**
     * @param Link  $link
     * @param User  $actor
     * @param array $data
     */
    public function __construct(Link $link, User $actor, array $data)
    {
        $this->link = $link;
        $this->actor = $actor;
        $this->data = $data;
    }
}
