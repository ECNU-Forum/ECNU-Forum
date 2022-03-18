<?php

/*
 * This file is part of fof/polls.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Polls\Commands;

use Carbon\Carbon;
use FoF\Polls\Poll;
use Illuminate\Support\Arr;

class EditPollHandler
{
    public function handle(EditPoll $command)
    {
        /**
         * @var $poll Poll
         */
        $poll = Poll::findOrFail($command->pollId);

        $command->actor->assertCan('edit', $poll);

        $attributes = Arr::get($command->data, 'attributes', []);
        $options = collect(Arr::get($attributes, 'options', []));

        if (isset($attributes['question'])) {
            $poll->question = $attributes['question'];
        }

        if (isset($attributes['publicPoll'])) {
            $poll->public_poll = (bool) $attributes['publicPoll'];
        }

        if (isset($attributes['endDate'])) {
            $endDate = $attributes['endDate'];

            if (is_string($endDate)) {
                $date = Carbon::createFromTimeString($attributes['endDate']);

                if (!$poll->hasEnded() && $date->isFuture() && ($poll->end_date === null || $poll->end_date->lessThanOrEqualTo($date))) {
                    $poll->end_date = $date;
                }
            } elseif (is_bool($endDate) && !$endDate) {
                $poll->end_date = null;
            }
        }

        $poll->save();

        // remove options not passed if 2 or more are
        if ($options->isNotEmpty() && $options->count() >= 2) {
            $ids = $options->pluck('id');

            $poll->options()->each(function ($option) use ($ids) {
                /*
                 * @var PollOption
                 */
                if (!$ids->contains($option->id)) {
                    $option->delete();
                }
            });
        }

        // update + add new options
        foreach ($options as $key => $opt) {
            $id = Arr::get($opt, 'id');
            $answer = Arr::get($opt, 'attributes.answer');

            $poll->options()->updateOrCreate([
                'id' => $id,
            ], [
                'answer' => $answer,
            ]);
        }

        return $poll;
    }
}
