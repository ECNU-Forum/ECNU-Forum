<?php

/*
 * This file is part of fof/polls.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Polls\Validators;

use Flarum\Foundation\AbstractValidator;

class PollOptionValidator extends AbstractValidator
{
    protected function getRules()
    {
        return [
            'answer' => 'required',
        ];
    }
}
