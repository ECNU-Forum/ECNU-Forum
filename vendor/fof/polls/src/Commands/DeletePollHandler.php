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

use FoF\Polls\Poll;

class DeletePollHandler
{
    public function handle(DeletePoll $command)
    {
        /**
         * @var $poll Poll
         */
        $poll = Poll::findOrFail($command->pollId);

        $command->actor->assertCan('delete', $poll);

        $poll->delete();
    }
}
