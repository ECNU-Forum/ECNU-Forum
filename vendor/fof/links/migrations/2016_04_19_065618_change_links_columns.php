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
        if ($schema->hasColumns('links', ['is_internal', 'is_newtab'])) {
            return;
        }

        $schema->table('links', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('ref_id');

            $table->boolean('is_internal')->default(0);
            $table->boolean('is_newtab')->default(0);
        });
    },

    'down' => function (Builder $schema) {
        $schema->table('links', function (Blueprint $table) {
            $table->string('type', 30);
            $table->integer('ref_id')->unsigned()->nullable();

            $table->dropColumn('is_internal');
            $table->dropColumn('is_newtab');
        });
    },
];
