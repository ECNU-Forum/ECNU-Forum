<?php

/*
 * This file is part of fof/byobu.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Byobu\Provider;

use Flarum\Discussion\Event\Saving;
use Flarum\Foundation\AbstractServiceProvider;
use FoF\Byobu\Discussion\Screener;
use FoF\Byobu\Listeners\DropTagsOnPrivateDiscussions;
use FoF\Byobu\Listeners\PersistRecipients;
use Illuminate\Events\Dispatcher;

class ByobuProvider extends AbstractServiceProvider
{
    public function register()
    {
        $this->container->bind('byobu.screener', Screener::class);
    }

    public function boot()
    {
        /** @var Dispatcher */
        $events = resolve(Dispatcher::class);

        // get the current listeners for the Discussion saving event
        $listeners = $events->getListeners(Saving::class);

        //remove current listeners
        $events->forget(Saving::class);

        // add byobu's persist recipients as the first listener, then add drop tags
        $events->listen(Saving::class, PersistRecipients::class);
        $events->listen(Saving::class, DropTagsOnPrivateDiscussions::class);

        // then re-add everything else
        foreach ($listeners as $listener) {
            $callable = function ($event, $payload = []) use ($listener) {
                return $listener($event, [$event, $payload]);
            };
            $events->listen(Saving::class, $callable);
        }
    }
}
