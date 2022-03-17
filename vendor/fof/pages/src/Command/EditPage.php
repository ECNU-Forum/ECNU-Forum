<?php

/*
 * This file is part of fof/pages.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Pages\Command;

use Flarum\User\User;

class EditPage
{
    /**
     * The ID of the page to edit.
     *
     * @var int
     */
    public $pageId;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * The attributes to update on the page.
     *
     * @var array
     */
    public $data;

    /**
     * @param int   $pageId The ID of the page to edit.
     * @param User  $actor  The user performing the action.
     * @param array $data   The attributes to update on the page.
     */
    public function __construct($pageId, User $actor, array $data)
    {
        $this->pageId = $pageId;
        $this->actor = $actor;
        $this->data = $data;
    }
}
