<?php

/*
 * This file is part of fof/links.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Links;

use Flarum\Foundation\AbstractValidator;

class LinkValidator extends AbstractValidator
{
    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'title' => ['required', 'string', 'max:50'],
        'url'   => ['string', 'max:255'],
        'icon'  => ['string', 'max:100'],
    ];
}
