<?php

/*
 * This file is part of fof/pages.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Pages\Command;

use Flarum\Settings\SettingsRepositoryInterface;
use FoF\Pages\PageRepository;

class DeletePageHandler
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var PageRepository
     */
    protected $pages;

    /**
     * @param PageRepository $pages
     */
    public function __construct(PageRepository $pages, SettingsRepositoryInterface $settings)
    {
        $this->pages = $pages;
        $this->settings = $settings;
    }

    /**
     * @param DeletePage $command
     *
     * @throws \Flarum\User\Exception\PermissionDeniedException
     *
     * @return \FoF\Pages\Page
     */
    public function handle(DeletePage $command)
    {
        $actor = $command->actor;

        $page = $this->pages->findOrFail($command->pageId, $actor);

        $actor->assertAdmin();

        // if it has been set as home page revert back to default router
        $homePage = intval($this->settings->get('pages_home'));
        if ($homePage && $page->id === $homePage) {
            $this->settings->delete('pages_home');
            $this->settings->set('default_route', '/all');
        }

        $page->delete();

        return $page;
    }
}
