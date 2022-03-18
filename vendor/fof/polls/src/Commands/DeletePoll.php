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

class DeletePoll
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
     * @param User $actor
     * @param int  $pollId
     */
    public function __construct(User $actor, int $pollId)
    {
        $this->actor = $actor;
        $this->pollId = $pollId;
    }
}
