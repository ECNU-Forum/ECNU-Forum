<?php

/*
 * This file is part of fof/nightmode.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\NightMode\Content;

use Flarum\Frontend\Document;

class HideBody
{
    /**
     * @param Document $document
     */
    public function __invoke(Document $document)
    {
        $isDay = false;
        $isNight = false;

        foreach ($document->head as $line) {
            if (!$isDay) {
                $isDay = (bool) strpos($line, $document->payload['fof-nightmode.assets.day']);
            }

            if (!$isNight) {
                $isNight = (bool) strpos($line, $document->payload['fof-nightmode.assets.night']);
            }
        }

        $hasStyle = $isDay || $isNight;

        if (!$hasStyle) {
            $document->meta['color-scheme'] = 'dark light';
        } elseif ($isDay) {
            $document->meta['color-scheme'] = 'light';
        } elseif ($isNight) {
            $document->meta['color-scheme'] = 'dark';
        }
    }
}
