<?php

/*
 * This file is part of fof/merge-discussions.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\MergeDiscussions\Validators;

use Flarum\Foundation\AbstractValidator;

class MergeDiscussionValidator extends AbstractValidator
{
    protected $rules = [
        'discussion_id' => [
            'int',
            'filled',
            'exists:discussions,id',
        ],
        'merging_discussions' => [
            'filled',
            'exists:discussions,id',
        ],
        'posts' => [
            'array',
            'filled',
        ],
    ];
}
