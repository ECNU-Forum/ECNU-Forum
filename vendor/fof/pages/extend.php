<?php

/*
 * This file is part of fof/pages.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Pages;

use Flarum\Extend;
use FoF\Pages\Api\Controller;

return [
    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\Frontend('admin'))
        ->css(__DIR__.'/resources/less/admin.less')
        ->js(__DIR__.'/js/dist/admin.js'),

    (new Extend\Frontend('forum'))
        ->css(__DIR__.'/resources/less/forum.less')
        ->js(__DIR__.'/js/dist/forum.js')
        ->route('/pages/home', 'pages.home')
        ->route('/p/{id:[\d\S]+(?:-[^/]*)?}', 'pages.page', Content\Page::class)
        ->content(Content\AddHomePageId::class),

    (new Extend\Routes('api'))
        ->get('/pages', 'pages.index', Controller\ListPagesController::class)
        ->post('/pages', 'pages.create', Controller\CreatePageController::class)
        ->get('/pages/{id}', 'pages.show', Controller\ShowPageController::class)
        ->patch('/pages/{id}', 'pages.update', Controller\UpdatePageController::class)
        ->delete('/pages/{id}', 'pages.delete', Controller\DeletePageController::class),

    (new Extend\View())
        ->namespace('fof-pages', __DIR__.'/resources/views'),

    (new Extend\ModelVisibility(Page::class))
        ->scope(Access\ScopePageVisibility::class),

    (new Extend\Filter(Search\PageFilterer::class))
        ->addFilter(Search\NoOpGambit::class),

    (new Extend\SimpleFlarumSearch(Search\PageSearcher::class))
        ->setFullTextGambit(Search\NoOpGambit::class),

    (new Extend\ServiceProvider())
        ->register(Providers\PageServiceProvider::class),
];
