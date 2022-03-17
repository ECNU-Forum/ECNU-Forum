<?php

/*
 * This file is part of fof/formatting.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Formatting\Listeners;

use Flarum\Settings\Event\Saved;

class ClearCache
{
    public function handle(Saved $event)
    {
        foreach ($event->settings as $key => $setting) {
            if (strpos($key, 'fof-formatting.plugin.') === 0) {
                resolve('flarum.formatter')->flush();

                return;
            }
        }
    }
}
