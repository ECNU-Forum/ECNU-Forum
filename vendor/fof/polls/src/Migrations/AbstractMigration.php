<?php

/*
 * This file is part of fof/polls.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Polls\Migrations;

use Illuminate\Database\Schema\Builder;

class AbstractMigration
{
    /**
     * @param callable $up       called if not migrating from reflar/polls
     * @param callable $existing called if migrating from reflar/polls
     * @param callable $down     called when rolling back migrations
     *
     * @return array
     */
    public static function make(callable $up, callable $existing, callable $down)
    {
        return [
            'up' => function (Builder $schema) use ($existing, $up) {
                if ($schema->getConnection()->table('migrations')->where('extension', 'reflar-polls')->exists()) {
                    $migrated = $schema->getConnection()->table('migrations')
                        ->where('migration', '2019_06_29_000000_add_support_for_deleted_users')
                        ->where('extension', 'reflar-polls')
                        ->exists();

                    if (!$migrated) {
                        throw new \UnexpectedValueException('[fof/polls] Please run the latest migration(s) of reflar/polls before enabling this extension.');
                    }

                    if ($existing != null) {
                        $existing($schema);
                    }

                    return;
                }

                $up($schema);
            },
            'down' => $down,
        ];
    }
}
