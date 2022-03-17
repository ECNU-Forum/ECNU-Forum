<?php

/*
 * This file is part of fof/pages.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Pages;

use Flarum\Foundation\AbstractValidator;

class PageValidator extends AbstractValidator
{
    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'title' => [
            'required',
            'max:200',
        ],
        'slug' => [
            'required',
            'unique:pages,slug',
            'max:200',
        ],
        'content' => [
            'required',
            'max:16777215',
        ],
    ];
}
