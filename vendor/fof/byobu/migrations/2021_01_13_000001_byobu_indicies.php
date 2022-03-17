<?php

/*
 * This file is part of fof/byobu.
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
        $schema->table('recipients', function (Blueprint $table) {
            $table->index('removed_at');
            $table->index(['discussion_id', 'user_id']);
            $table->index(['discussion_id', 'group_id']);
        });
    },
    'down' => function (Builder $schema) {
        $schema->table('recipients', function (Blueprint $table) {
            $table->dropIndex(['removed_at']);
        });
    },
];
