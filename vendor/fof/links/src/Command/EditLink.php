<?php

/*
 * This file is part of fof/links.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Links\Command;

use Flarum\User\User;

class EditLink
{
    /**
     * The ID of the link to edit.
     *
     * @var int
     */
    public $linkId;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * The attributes to update on the link.
     *
     * @var array
     */
    public $data;

    /**
     * @param int   $linkId The ID of the link to edit.
     * @param User  $actor  The user performing the action.
     * @param array $data   The attributes to update on the link.
     */
    public function __construct($linkId, User $actor, array $data)
    {
        $this->linkId = $linkId;
        $this->actor = $actor;
        $this->data = $data;
    }
}
