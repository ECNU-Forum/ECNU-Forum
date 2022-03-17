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
        if ($schema->hasColumn('links', 'parent_id')) {
            return;
        }

        $schema->table('links', function (Blueprint $table) {
            $table->unsignedInteger('parent_id')->nullable();

            $table->foreign('parent_id')->references('id')->on('links')->onDelete('set null');
        });
    },
    'down' => function (Builder $schema) {
        $schema->table('links', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);

            $table->dropColumn('parent_id');
        });
    },
];
