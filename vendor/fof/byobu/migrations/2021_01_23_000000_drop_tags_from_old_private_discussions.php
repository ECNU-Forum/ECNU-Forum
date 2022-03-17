<?php

/*
 * This file is part of fof/byobu.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $connection = $schema->getConnection();
        $prefix = $connection->getTablePrefix();

        $connection->statement("DELETE FROM {$prefix}discussion_tag WHERE discussion_id IN (SELECT DISTINCT(discussion_id) FROM {$prefix}recipients)");
    },
    'down' => function (Builder $schema) {
        //
    },
];
