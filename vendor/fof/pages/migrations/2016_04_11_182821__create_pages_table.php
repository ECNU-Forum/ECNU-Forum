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
        if ($schema->hasTable('pages')) {
            return;
        }

        $schema->create('pages', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title', 200);
            $table->string('slug', 200);
            $table->dateTime('time');
            $table->dateTime('edit_time')->nullable();
            $table->text('content')->nullable();

            $table->boolean('is_hidden')->default(0);
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('pages');
    },
];
