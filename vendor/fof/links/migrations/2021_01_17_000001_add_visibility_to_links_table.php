<?php

/*
 * This file is part of fof/links.
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
        if ($schema->hasColumn('links', 'visibility')) {
            return;
        }

        $schema->table('links', function (Blueprint $table) {
            $table->enum('visibility', ['everyone', 'members', 'guests'])->default('everyone');
            $table->index('visibility');
        });
    },

    'down' => function (Builder $schema) {
        $schema->table('links', function (Blueprint $table) {
            $table->dropIndex(['visibility']);
            $table->dropColumn('visibility');
        });
    },
];
