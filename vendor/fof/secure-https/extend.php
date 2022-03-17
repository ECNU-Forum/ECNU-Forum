<?php

/*
 * This file is part of fof/secure-https.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\SecureHttps;

use Flarum\Api\Serializer\BasicPostSerializer;
use Flarum\Extend;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\Routes('api'))
        ->get(
            '/fof/secure-https',
            'fof.secure-https.imgurl',
            Api\Controllers\GetImageUrlController::class
        ),

    (new Extend\Formatter())
        ->render(FormatImages::class),

    (new Extend\Middleware('forum'))
        ->add(Middlewares\ContentSecurityPolicyMiddleware::class),

    (new Extend\ApiSerializer(BasicPostSerializer::class))
        ->attributes(ModifyContentHtml::class),
];
