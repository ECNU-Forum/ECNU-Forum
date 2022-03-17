<?php

/*
 * This file is part of fof/reactions.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Reactions\Command;

use Flarum\User\Exception\PermissionDeniedException;
use FoF\Reactions\Reaction;
use FoF\Reactions\Validator\ReactionValidator;

class EditReactionHandler
{
    /**
     * @var ReactionValidator
     */
    protected $validator;

    /**
     * @param ReactionValidator $validator
     */
    public function __construct(ReactionValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param EditReaction $command
     *
     * @throws PermissionDeniedException
     *
     * @return Reaction
     */
    public function handle(EditReaction $command)
    {
        $actor = $command->actor;
        $data = $command->data;

        $actor->assertAdmin();

        $reaction = Reaction::where('id', $command->reactionId)->first();

        if (isset($data['identifier'])) {
            $reaction->identifier = $data['identifier'];
        }

        if (isset($data['display'])) {
            $reaction->display = $data['display'];
        }

        if (isset($data['type'])) {
            $reaction->type = $data['type'];
        }

        if (isset($data['enabled'])) {
            $reaction->enabled = $data['enabled'];
        }

        $this->validator->assertValid($reaction->getDirty());

        $reaction->save();

        return $reaction;
    }
}
