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
use Flarum\Api\Serializer\BasicUserSerializer;
use FoF\Polls\PollVote;

class PollVoteSerializer extends AbstractSerializer
{
    /**
     * @var string
     */
    protected $type = 'poll_votes';

    /**
     * Get the default set of serialized attributes for a model.
     *
     * @param PollVote $vote
     *
     * @return array
     */
    protected function getDefaultAttributes($vote)
    {
        return [
            'pollId'    => $vote->poll_id,
            'optionId'  => $vote->option_id,
            'createdAt' => $this->formatDate($vote->created_at),
            'updatedAt' => $this->formatDate($vote->updated_at),
        ];
    }

    public function poll($model)
    {
        return $this->hasOne(
            $model,
            PollSerializer::class
        );
    }

    public function option($model)
    {
        return $this->hasOne(
            $model,
            PollOptionSerializer::class
        );
    }

    public function user($model)
    {
        return $this->hasOne(
            $model,
            BasicUserSerializer::class
        );
    }
}
