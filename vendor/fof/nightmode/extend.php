<?php

/*
 * This file is part of fof/nightmode.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\NightMode;

use Flarum\Extend;
use Flarum\Settings\SettingsRepositoryInterface;
use FoF\Extend\Extend as FoFExtend;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->content(Content\HideBody::class)
        ->content(Content\PatchUnsupportedAutoNightmode::class)
        ->css(__DIR__.'/resources/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->content(Content\HideBody::class)
        ->content(Content\PatchUnsupportedAutoNightmode::class),

    new Extend\Locales(__DIR__.'/resources/locale'),

    (new FoFExtend\ExtensionSettings())
        ->addKey('fof-nightmode.default_theme'),

    (new Extend\ServiceProvider())
        ->register(AssetsServiceProvider::class),

    (new Extend\User())
        ->registerPreference('fofNightMode', function ($value) {
            if ($value === '' || $value === null) {
                $value = (int) resolve(SettingsRepositoryInterface::class)->get('fof-nightmode.default_theme', 0);
            }

            return (int) $value;
        })
        ->registerPreference('fofNightMode_perDevice', null, false),

    (new Extend\Settings())
        ->serializeToForum('fofNightMode_autoUnsupportedFallback', 'theme_dark_mode', function ($val) {
            $val = (bool) $val;

            // 2 = night mode, 1 = light mode, 0 = auto
            if ($val) {
                return 2;
            }

            return 1;
        }, false),
];
