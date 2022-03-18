<?php

/*
 * This file is part of fof/polls.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Polls\Commands;

use Flarum\User\User;

class EditPoll
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var int
     */
    public $pollId;

    /**
     * @var array
     */
    public $data;

    /**
     * @param User  $actor
     * @param int   $pollId
     * @param array $data
     */
    public function __construct(User $actor, int $pollId, array $data)
    {
        $this->actor = $actor;
        $this->pollId = $pollId;
        $this->data = $data;
    }
}
