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
use FoF\Polls\PollOption;

class PollOptionSerializer extends AbstractSerializer
{
    /**
     * @var string
     */
    protected $type = 'poll_options';

    /**
     * Get the default set of serialized attributes for a model.
     *
     * @param PollOption $option
     *
     * @return array
     */
    protected function getDefaultAttributes($option)
    {
        $attributes = [
            'answer'    => $option->answer,
            'createdAt' => $this->formatDate($option->created_at),
            'updatedAt' => $this->formatDate($option->updated_at),
        ];

        if ($this->actor->can('seeVoteCount', $option->poll)) {
            $attributes['voteCount'] = (int) $option->vote_count;
        }

        return $attributes;
    }
}
