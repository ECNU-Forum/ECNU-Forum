<?php

/*
 * This file is part of fof/pages.
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
        if ($schema->hasColumn('pages', 'is_html')) {
            return;
        }

        $schema->table('pages', function (Blueprint $table) {
            $table->boolean('is_html')->default(0);
        });
    },
    'down' => function (Builder $schema) {
        $schema->table('pages', function (Blueprint $table) {
            $table->removeColumn('is_html');
        });
    },
];
