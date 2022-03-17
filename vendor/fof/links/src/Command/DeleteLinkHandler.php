<?php

/*
 * This file is part of fof/links.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Links\Command;

use FoF\Links\Event\Deleted;
use FoF\Links\Event\Deleting;
use FoF\Links\LinkRepository;
use Illuminate\Contracts\Events\Dispatcher;

class DeleteLinkHandler
{
    /**
     * @var LinkRepository
     */
    protected $links;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * @param LinkRepository $links
     */
    public function __construct(LinkRepository $links, Dispatcher $events)
    {
        $this->links = $links;
        $this->events = $events;
    }

    /**
     * @param DeleteLink $command
     *
     * @throws \Flarum\User\Exception\PermissionDeniedException
     *
     * @return \FoF\Links\Link
     */
    public function handle(DeleteLink $command)
    {
        $actor = $command->actor;

        $link = $this->links->findOrFail($command->linkId, $actor);

        $actor->assertAdmin();

        $this->events->dispatch(new Deleting($link, $actor, []));

        $link->delete();

        $this->events->dispatch(new Deleted($link, $actor, []));

        return $link;
    }
}
