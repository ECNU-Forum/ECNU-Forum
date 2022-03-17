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

class DeleteReactionHandler
{
    /**
     * @param DeleteReaction $command
     *
     * @throws PermissionDeniedException
     *
     * @return Reaction
     */
    public function handle(DeleteReaction $command)
    {
        $actor = $command->actor;

        $actor->assertAdmin();

        $reaction = Reaction::where('id', $command->reactionId)->first();

        $reaction->delete();
    }
}
