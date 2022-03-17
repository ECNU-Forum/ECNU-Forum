<?php

/*
 * This file is part of fof/merge-discussions.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Flarum\Database\Migration;
use Flarum\Discussion\Discussion;
use Illuminate\Database\Schema\Blueprint;

return Migration::createTable('fof_merge_discussions_redirections', function (Blueprint $table) {
    $table->increments('id');
    $table->foreignIdFor(Discussion::class, 'request_discussion_id');
    $table->foreignIdFor(Discussion::class, 'to_discussion_id');
    $table->unsignedInteger('http_code');
    $table->timestamp('created_at');
});
