<?php

/*
 * This file is part of fof/polls.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use FoF\Polls\Migrations\AbstractMigration;
use FoF\Polls\PollOption;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return AbstractMigration::make(
    function (Builder $schema) {
        $schema->create('poll_options', function (Blueprint $table) {
            $table->increments('id');

            $table->string('answer');

            $table->integer('poll_id')->unsigned();

            $table->timestamps();

            $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
        });
    },
    function (Builder $schema) {
        // delete poll options that don't have a poll
        PollOption::query()->doesntHave('poll')->delete();

        $schema->table('poll_options', function (Blueprint $table) {
            $table->dropForeign(['poll_id']);
        });

        $schema->table('poll_options', function (Blueprint $table) {
            $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
        });
    },
    function (Builder $schema) {
        $schema->dropIfExists('poll_options');
    }
);
