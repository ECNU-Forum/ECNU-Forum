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

class DeleteLink
{
    /**
     * The ID of the link to delete.
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
     * Any other link input associated with the action. This is unused by
     * default, but may be used by extensions.
     *
     * @var array
     */
    public $data;

    /**
     * @param int   $linkId The ID of the link to delete.
     * @param User  $actor  The user performing the action.
     * @param array $data   Any other link input associated with the action. This
     *                      is unused by default, but may be used by extensions.
     */
    public function __construct($linkId, User $actor, array $data = [])
    {
        $this->linkId = $linkId;
        $this->actor = $actor;
        $this->data = $data;
    }
}
