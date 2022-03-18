<?php

/*
 * This file is part of fof/polls.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Polls\Access;

use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;
use FoF\Polls\Poll;

class PollPolicy extends AbstractPolicy
{
    public function seeVoteCount(User $actor, Poll $poll)
    {
        if ($actor->can('viewPollResultsWithoutVoting')) {
            return $this->allow();
        }

        if ($poll->myVotes($actor)->count()) {
            return $this->allow();
        }
    }

    public function seeVotes(User $actor, Poll $poll)
    {
        if (($poll->myVotes($actor)->count() || $actor->can('viewPollResultsWithoutVoting')) && $poll->public_poll) {
            return $this->allow();
        }
    }

    public function vote(User $actor, Poll $poll)
    {
        if ($actor->hasPermission('votePolls') && !$poll->hasEnded()) {
            return $this->allow();
        }
    }

    public function changeVote(User $actor, Poll $poll)
    {
        if ($actor->hasPermission('changeVotePolls')) {
            return $this->allow();
        }
    }

    public function edit(User $actor, Poll $poll)
    {
        if ($actor->hasPermission('discussion.polls')) {
            return $this->allow();
        }

        if ($actor->hasPermission('selfEditPolls') && !$poll->hasEnded()) {
            $ownerId = $poll->discussion->user_id;

            if ($ownerId && $ownerId === $actor->id) {
                return $this->allow();
            }
        }
    }

    public function delete(User $actor, Poll $poll)
    {
        return $this->edit($actor, $poll);
    }
}
