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
        if ($schema->hasColumn('created_at', 'updated_at')) {
            return;
        }

        $schema->table('post_reactions', function (Blueprint $table) {
            $table->timestamps();
        });
    },

    'down' => function (Builder $schema) {
        $schema->table('post_reactions', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    },
];
