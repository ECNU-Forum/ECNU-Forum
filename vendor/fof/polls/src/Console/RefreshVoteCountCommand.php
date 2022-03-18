<?php

/*
 * This file is part of fof/polls.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Polls\Console;

use FoF\Polls\Poll;
use FoF\Polls\PollOption;
use Illuminate\Console\Command;

class RefreshVoteCountCommand extends Command
{
    protected $signature = 'fof:polls:refresh';

    protected $description = 'Re-calculate the total number of votes per option';

    public function handle()
    {
        $progress = $this->output->createProgressBar(Poll::query()->count() + PollOption::query()->count());

        Poll::query()->each(function (Poll $poll) use ($progress) {
            $poll->refreshVoteCount()->save();

            $progress->advance();
        });

        PollOption::query()->each(function (PollOption $option) use ($progress) {
            $option->refreshVoteCount()->save();

            $progress->advance();
        });

        $progress->finish();

        $this->info('Done.');
    }
}
