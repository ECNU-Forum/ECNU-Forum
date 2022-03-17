<?php

/*
 * This file is part of fof/pages.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Pages\Search;

use Flarum\Search\AbstractSearcher;
use Flarum\Search\GambitManager;
use Flarum\User\User;
use FoF\Pages\PageRepository;
use Illuminate\Database\Eloquent\Builder;

class PageSearcher extends AbstractSearcher
{
    /**
     * @var PageRepository
     */
    protected $pages;

    public function __construct(GambitManager $gambits, array $searchMutators, PageRepository $pages)
    {
        parent::__construct($gambits, $searchMutators);

        $this->pages = $pages;
    }

    protected function getQuery(User $actor): Builder
    {
        return $this->pages->query();
    }
}
