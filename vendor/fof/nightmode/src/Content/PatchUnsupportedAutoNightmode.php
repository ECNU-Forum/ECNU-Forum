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
use Flarum\Settings\SettingsRepositoryInterface;

class PatchUnsupportedAutoNightmode
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param Document $document
     */
    public function __invoke(Document $document)
    {
        // This JS snippet is a workaround for browsers which do not support the `prefers-color-scheme` CSS media query.
        //
        // For info about how the `matchmedia()` call works, see:
        // https://kilianvalkhof.com/2021/web/detecting-media-query-support-in-css-and-javascript/#detecting-media-query-support-in-java-script

        $class = $this->settings->get('theme_dark_mode', false) ? 'nightmode-dark' : 'nightmode-light';

        $document->head[] = "
        <script>
            /* fof/nightmode workaround for browsers without (prefers-color-scheme) CSS media query support */
            if (!window.matchMedia('not all and (prefers-color-scheme), (prefers-color-scheme)').matches) {
                document.querySelector('link.$class').removeAttribute('media');
            }
        </script>
        ";
    }
}
