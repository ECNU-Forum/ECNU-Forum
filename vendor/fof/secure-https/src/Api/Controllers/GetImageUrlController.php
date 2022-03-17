<?php

/*
 * This file is part of fof/secure-https.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\SecureHttps\Api\Controllers;

use Flarum\Http\RequestUtil;
use Flarum\User\User;
use FoF\SecureHttps\Exceptions\ImageNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetImageUrlController implements RequestHandlerInterface
{
    /**
     * Handle the request and return a response.
     *
     * @param ServerRequestInterface $request
     *
     * @throws \Flarum\User\Exception\PermissionDeniedException
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var User
         */
        $actor = RequestUtil::getActor($request);

        $actor->assertCan('viewForum');

        $imgurl = urldecode(Arr::get($request->getQueryParams(), 'imgurl'));
        $host = parse_url($imgurl, PHP_URL_HOST);
        $isIP = filter_var(
            $host,
            FILTER_VALIDATE_IP
        );
        $isPublicIP = filter_var(
            $host,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );

        if (!preg_match('/^https?:\/\//', $imgurl) || ($isIP && !$isPublicIP)) {
            throw new ImageNotFoundException();
        }

        $client = new Client();

        try {
            $res = $client->request('GET', $imgurl, [
                'headers' => [
                    'Accept' => 'image/*',
                ],
            ]);
        } catch (GuzzleException $e) {
            if ($e->getCode() > 0 && $e->getCode() < 500) {
                throw new ImageNotFoundException();
            }

            throw $e;
        }

        $type = $res->getHeaderLine('Content-Type');
        $contents = $res->getBody();

        if (!Str::startsWith($type, 'image/') || !$contents->getSize()) {
            throw new ImageNotFoundException();
        }

        return new Response(
            $res->getStatusCode(),
            [
                'Content-Type' => $res->getHeaderLine('Content-Type'),
            ],
            $contents
        );
    }
}
