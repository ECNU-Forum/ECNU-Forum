<?php

/*
 * This file is part of fof/reactions.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $schema->table('reactions', function (Blueprint $table) {
            $table->boolean('enabled')->default(true);
        });
    },

    'down' => function (Builder $schema) {
        $schema->table('reactions', function (Blueprint $table) {
            $table->dropColumn('enabled');
        });
    },
];
