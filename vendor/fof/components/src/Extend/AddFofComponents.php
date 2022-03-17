<?php

/*
 * This file is part of fof/components.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Components\Extend;

use Flarum\Extend\ExtenderInterface;
use Flarum\Extension\Extension;
use Flarum\Frontend\Assets;
use Flarum\Frontend\Compiler\Source\SourceCollector;
use Illuminate\Contracts\Container\Container;

class AddFofComponents implements ExtenderInterface
{
    public static $initialized = false;

    public function extend(Container $container, Extension $extension = null)
    {
        if (self::$initialized) {
            return;
        }

        self::$initialized = true;

        $container->resolving('flarum.assets.admin', function (Assets $assets) {
            $assets->js(function (SourceCollector $sources) {
                $sources->addString(function () {
                    return 'var module={}';
                });
                $sources->addFile(__DIR__.'/../../js/dist/admin.js');
                $sources->addString(function () {
                    return "flarum.extensions['fof-components']=module.exports";
                });
            });
        });
    }
}
