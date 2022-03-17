<?php

/*
 * This file is part of fof/byobu.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Byobu\Listeners;

use Flarum\Post\Event\Saving;
use FoF\Byobu\Concerns\ExtensionsDiscovery;
use FoF\Byobu\Discussion\Screener;

class IgnoreApprovals
{
    use ExtensionsDiscovery;

    public function handle(Saving $event)
    {
        /** @var Screener $screener */
        $screener = resolve('byobu.screener');
        $screener = $screener->fromDiscussion($event->post->discussion);

        if ($this->extensionIsEnabled('flarum-approval') && $screener->isPrivate()) {
            $event->post->is_approved = true;
        }
    }
}
