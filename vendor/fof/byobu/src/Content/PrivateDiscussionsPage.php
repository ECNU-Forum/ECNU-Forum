<?php

/*
 * This file is part of fof/byobu.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Byobu\Content;

use Flarum\Forum\Content\Index;
use Flarum\Frontend\Document;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface as Request;

class PrivateDiscussionsPage extends Index
{
    public function __invoke(Document $document, Request $request)
    {
        $queryParams = $request->getQueryParams();
        $q = Arr::pull($queryParams, 'q', '');
        Arr::set($queryParams, 'q', "$q is:private");

        $request = $request->withQueryParams($queryParams);

        return parent::__invoke($document, $request);
    }
}
