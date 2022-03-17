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

use Flarum\Api\Serializer\ForumSerializer;

class FormatterConfigurator
{
    const PLUGINS = [
        'Autoimage',
        'Autovideo',
        'FancyPants',
        'HTMLEntities',
        'MediaEmbed',
        'PipeTables',
        'TaskLists',
    ];

    public function __invoke(ForumSerializer $serializer): array
    {
        $attributes = [];

        if ($serializer->getActor()->isAdmin()) {
            $attributes['fof-formatting.plugins'] = self::PLUGINS;
        }

        return $attributes;
    }
}
