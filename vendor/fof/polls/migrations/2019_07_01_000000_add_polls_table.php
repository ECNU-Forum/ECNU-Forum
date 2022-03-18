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
use FoF\Polls\Poll;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return AbstractMigration::make(
    function (Builder $schema) {
        $schema->create('polls', function (Blueprint $table) {
            $table->increments('id');

            $table->string('question');

            $table->integer('discussion_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();

            $table->boolean('public_poll');

            $table->timestamp('end_date')->nullable();
            $table->timestamps();

            $table->foreign('discussion_id')->references('id')->on('discussions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    },
    function (Builder $schema) {
        // delete polls whose discussion was deleted
        $polls = Poll::doesntHave('discussion')->get();

        if ($polls->count() !== 0) {
            resolve('log')->info("[fof/polls] deleting {$polls->count()} polls");

            foreach ($polls as $poll) {
                /*
                 * @var Poll $poll
                 */
                resolve('log')->info("[fof/polls] |> deleting #{$poll->id}");

                $poll->votes()->delete();
                $poll->options()->delete();
                $poll->delete();
            }
        }

        // set user to null if user was deleted
        Poll::query()->whereNotNull('user_id')->doesntHave('user')->update([
            'user_id' => null,
        ]);

        $schema->table('polls', function (Blueprint $table) {
            $table->integer('discussion_id')->unsigned()->change();
            $table->integer('user_id')->unsigned()->nullable()->change();

            $table->foreign('discussion_id')->references('id')->on('discussions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    },
    function (Builder $schema) {
        $schema->dropIfExists('polls');
    }
);
