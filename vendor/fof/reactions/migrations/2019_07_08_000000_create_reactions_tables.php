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
        if ($schema->hasTable('reactions')) {
            return;
        }

        $schema->create('reactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identifier');
            $table->string('type');
        });

        /*
         *  Identifier can be an emoji name, or font-awesome icon
         *  Type is either emoji, or FA
        **/

        $schema->getConnection()->table('reactions')->insert([
            ['identifier' => 'thumbsup', 'type' => 'emoji'],
            ['identifier' => 'thumbsdown', 'type' => 'emoji'],
            ['identifier' => 'laughing', 'type' => 'emoji'],
            ['identifier' => 'confused', 'type' => 'emoji'],
            ['identifier' => 'heart', 'type' => 'emoji'],
            ['identifier' => 'tada', 'type' => 'emoji'],
        ]);
    },

    'down' => function (Builder $schema) {
        $schema->dropIfExists('reactions');
    },
];
