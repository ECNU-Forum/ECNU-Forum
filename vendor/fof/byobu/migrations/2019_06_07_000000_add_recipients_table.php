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
        // in case someone updates from really old flagrow/byobu version, set is_private to 1 on private discussions
        $connection = $schema->getConnection();

        if ($connection->getSchemaBuilder()->hasTable('recipients')) {
            $ids = $connection->table('recipients')
                ->select('discussion_id')
                ->groupBy('discussion_id')
                ->get();

            $connection->table('discussions')
                ->whereIn('id', collect($ids)->pluck('discussion_id')->all())
                ->update(['is_private' => 1]);
        } else {
            $schema->create('recipients', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('discussion_id')->unsigned()->nullable();
                $table->integer('user_id')->unsigned();
                $table->integer('group_id')->unsigned()->nullable();
                $table->timestamps();
                $table->timestamp('removed_at')->nullable();

                $table->foreign('discussion_id')
                    ->references('id')
                    ->on('discussions')
                    ->onDelete('cascade');
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
                $table->foreign('group_id')
                    ->references('id')
                    ->on('groups')
                    ->onDelete('cascade');
            });
        }
    },
    'down' => function (Builder $schema) {
        $schema->drop('recipients');
    },
];
