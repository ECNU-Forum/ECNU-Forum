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

use Flarum\Discussion\Discussion;
use FoF\Byobu\Discussion\Screener;

class GetModelIsPrivate
{
    public function __invoke(Discussion $discussion)
    {
        /** @var Screener $screener */
        $screener = resolve('byobu.screener');
        $screener = $screener->fromDiscussion($discussion);

        // Unless we think it's private, delegate the check further
        // along the pipeline to other listeners.
        return $screener->isPrivate();
    }
}
