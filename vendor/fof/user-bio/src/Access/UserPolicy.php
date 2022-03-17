<?php

/*
 * This file is part of fof/user-bio.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\UserBio\Access;

use Carbon\Carbon;
use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

class UserPolicy extends AbstractPolicy
{
    public function viewBio(User $actor, User $user)
    {
        // Suspended users won't show their Bio, unless you are allowed to edit any bio.
        if (!$actor->hasPermission('fof-user-bio.editAny') && $this->isSuspended($user)) {
            return $this->deny();
        }

        // We only let the user see its own bio if they are also allowed to edit it
        if (($actor->id === $user->id && $actor->hasPermission('fof-user-bio.editOwn'))
            || $actor->hasPermission('fof-user-bio.view')
        ) {
            return $this->allow();
        }

        return $this->deny();
    }

    public function editBio(User $actor, User $user)
    {
        if (($actor->id === $user->id
                && $actor->hasPermission('fof-user-bio.editOwn')
                && !$this->isSuspended($user))
            || $actor->hasPermission('fof-user-bio.editAny')) {
            return $this->allow();
        }

        return $this->deny();
    }

    protected function isSuspended(User $user): bool
    {
        // suspended_until is null if flarum/suspend isn't installed
        // laravel sets all non existing attributes to null
        // suspend_until is also null if the user isn't suspended.
        return $user->suspended_until !== null
            && $user->suspended_until instanceof Carbon
            && $user->suspended_until->isFuture();
    }
}
