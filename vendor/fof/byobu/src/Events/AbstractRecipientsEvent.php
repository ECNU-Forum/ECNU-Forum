<?php

/*
 * This file is part of fof/byobu.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Byobu\Events;

use Flarum\Discussion\Discussion;
use FoF\Byobu\Discussion\Screener;

abstract class AbstractRecipientsEvent
{
    /**
     * @var Discussion
     */
    public $discussion;

    /**
     * @var Screener
     */
    public $screener;

    public function __construct(Discussion $discussion, Screener $screener)
    {
        $this->discussion = $discussion;
        $this->screener = $screener;
    }
}
