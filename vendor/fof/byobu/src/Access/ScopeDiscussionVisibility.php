<?php

/*
 * This file is part of fof/byobu.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Byobu\Access;

use Flarum\User\User;
use FoF\Byobu\Database\RecipientsConstraint;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class ScopeDiscussionVisibility
{
    use RecipientsConstraint;

    /**
     * @param User            $actor
     * @param EloquentBuilder $query
     */
    public function __invoke(User $actor, EloquentBuilder $query)
    {
        $this->constraint($query, $actor);
    }
}
