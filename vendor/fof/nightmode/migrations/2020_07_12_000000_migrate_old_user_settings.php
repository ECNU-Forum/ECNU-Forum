<?php

/*
 * This file is part of fof/nightmode.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Flarum\User\User;
use Illuminate\Database\Schema\Builder;

const KEY = 'fofNightMode';

return [
    'up' => function (Builder $schema) {
        User::query()
            ->where('preferences', 'LIKE', sprintf('%%"%s":%%', KEY))
            ->chunk(100, function ($users) {
                /**
                 * @var User $user
                 */
                foreach ($users as $user) {
                    $value = $user->getPreference(KEY);

                    if (!is_bool($value)) {
                        continue;
                    }

                    $theme = $value === true
                        ? 2 // dark mode if setting is set
                        : 1; // light mode by default

                    $user->setPreference(KEY, $theme)->save();
                }
            });
    },
    'down' => function (Builder $schema) {
        //
    },
];
