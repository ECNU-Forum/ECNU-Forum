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
        if ($schema->hasColumn('links', 'icon')) {
            return;
        }

        $schema->table('links', function (Blueprint $table) {
            $table->string('icon', 100)->nullable();
        });
    },

    'down' => function (Builder $schema) {
        $schema->table('links', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    },
];
