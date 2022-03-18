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
use FoF\Polls\PollVote;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return AbstractMigration::make(
    function (Builder $schema) {
        $schema->create('poll_votes', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('poll_id')->unsigned();
            $table->integer('option_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();

            $table->timestamps();

            $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('poll_options')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    },
    function (Builder $schema) {
        // delete votes that don't have a poll or option
        PollVote::query()->doesntHave('poll')->orDoesntHave('option')->delete();

        $schema->table('poll_votes', function (Blueprint $table) {
            $table->dropForeign(['option_id']);
        });

        $schema->table('poll_votes', function (Blueprint $table) {
            $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('poll_options')->onDelete('cascade');
        });
    },
    function (Builder $schema) {
        $schema->dropIfExists('poll_votes');
    }
);
