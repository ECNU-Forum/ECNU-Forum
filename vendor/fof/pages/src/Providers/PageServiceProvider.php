<?php

/*
 * This file is part of fof/pages.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Pages\Providers;

use Flarum\Formatter\Formatter;
use Flarum\Foundation\AbstractServiceProvider;
use Flarum\Foundation\Paths;
use FoF\Pages\Page;

class PageServiceProvider extends AbstractServiceProvider
{
    public function boot()
    {
        $this->container->instance('path.pages', ($this->container->make(Paths::class))->base.DIRECTORY_SEPARATOR.'pages');

        Page::setFormatter($this->container->make(Formatter::class));
    }
}
