<?php

/*
 * This file is part of fof/reactions.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        /**
         * @var \Flarum\Settings\SettingsRepositoryInterface
         */
        $settings = resolve('flarum.settings');

        $keys = ['convertToUpvote', 'convertToDownvote', 'convertToLike'];

        foreach ($keys as $key) {
            if ($value = $settings->get($full = "reflar.reactions.$key")) {
                $settings->set("fof-reactions.$key", $value);
                $settings->delete($full);
            }
        }
    },
];
