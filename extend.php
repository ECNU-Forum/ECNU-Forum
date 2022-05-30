<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

use Flarum\Extend;
use Flarum\Api\Serializer\PostSerializer;

return [
    // Register extenders here to customize your forum!
    (new Extend\ApiSerializer(PostSerializer::class))
        ->attribute('content', function ($serializer, $post, $attributes) {
            return $post->content;
        })
];
