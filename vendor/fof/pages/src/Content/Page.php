<?php

/*
 * This file is part of fof/pages.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Pages\Content;

use Flarum\Api\Client;
use Flarum\Frontend\Document;
use Flarum\Http\Exception\RouteNotFoundException;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

class Page
{
    /**
     * @var Client
     */
    protected $api;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var Factory
     */
    protected $view;

    public function __construct(Client $api, UrlGenerator $url, SettingsRepositoryInterface $settings, Factory $view)
    {
        $this->api = $api;
        $this->url = $url;
        $this->settings = $settings;
        $this->view = $view;
    }

    public function __invoke(Document $document, ServerRequestInterface $request)
    {
        $queryParams = $request->getQueryParams();

        $id = Arr::get($queryParams, 'id') ?? $this->settings->get('pages_home');

        $apiDocument = $this->getApiDocument($request, $id);

        $document->title = $apiDocument->data->attributes->title;

        $document->content = $this->view->make('fof-pages::content.page', compact('apiDocument'));

        $document->payload['apiDocument'] = $apiDocument;
    }

    private function getApiDocument(ServerRequestInterface $request, $id)
    {
        $response = $this->api->withParentRequest($request)->get('/pages/'.$id);

        if ($response->getStatusCode() === 404) {
            throw new RouteNotFoundException();
        }

        return json_decode($response->getBody());
    }
}
