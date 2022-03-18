<?php

/*
 * This file is part of fof/polls.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Polls\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use FoF\Polls\Poll;

class PollSerializer extends AbstractSerializer
{
    /**
     * @var string
     */
    protected $type = 'polls';

    /**
     * Get the default set of serialized attributes for a model.
     *
     * @param Poll $poll
     *
     * @return array
     */
    protected function getDefaultAttributes($poll)
    {
        $attributes = [
            'question'      => $poll->question,
            'hasEnded'      => $poll->hasEnded(),
            'publicPoll'    => (bool) $poll->public_poll,
            'endDate'       => $this->formatDate($poll->end_date),
            'createdAt'     => $this->formatDate($poll->created_at),
            'updatedAt'     => $this->formatDate($poll->updated_at),
            'canEdit'       => $this->actor->can('edit', $poll),
            'canDelete'     => $this->actor->can('delete', $poll),
            'canSeeVotes'   => $this->actor->can('seeVotes', $poll),
            'canChangeVote' => $this->actor->can('changeVote', $poll),
        ];

        if ($this->actor->can('seeVoteCount', $poll)) {
            $attributes['voteCount'] = (int) $poll->vote_count;
        }

        return $attributes;
    }

    public function options($model)
    {
        return $this->hasMany(
            $model,
            PollOptionSerializer::class
        );
    }

    public function votes($model)
    {
        if ($this->actor->cannot('seeVotes', $model)) {
            return null;
        }

        return $this->hasMany(
            $model,
            PollVoteSerializer::class
        );
    }

    public function myVotes($model)
    {
        Poll::setStateUser($this->actor);

        // When called inside ShowDiscussionController, Flarum has already pre-loaded our relationship incorrectly
        $model->unsetRelation('myVotes');

        return $this->hasMany(
            $model,
            PollVoteSerializer::class
        );
    }
}
