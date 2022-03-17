<?php

/*
 * This file is part of fof/byobu.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Byobu\Gambits\User;

use Flarum\Extension\ExtensionManager;
use Flarum\Search\AbstractRegexGambit;
use Flarum\Search\SearchState;
use FoF\Byobu\Events\SearchingRecipient;
use Illuminate\Contracts\Events\Dispatcher;

class AllowsPdGambit extends AbstractRegexGambit
{
    /**
     * @var Dispatcher
     */
    public $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function getGambitPattern()
    {
        return 'allows-pd';
    }

    protected function conditions(SearchState $search, array $matches, $negate)
    {
        $actor = $search->getActor();

        $this->dispatcher->dispatch(new SearchingRecipient($search, $matches, $negate));

        if ($actor->can('startPrivateDiscussionWithBlockers')) {
            return;
        }

        $search
            ->getQuery()
            // Always prevent PD's by non-privileged users to suspended users.
            ->when(
                $this->extensionEnabled('flarum-suspend') && !$negate,
                function ($query) {
                    $query->whereNull('suspended_until');
                }
            )
            // Always prevent PD's by non-privileged users to users that block PD's.
            ->where(function ($query) use ($negate) {
                $query->where('blocks_byobu_pd', $negate ? '1' : '0');
            })
            ->orderBy('username', 'asc');
    }

    protected function extensionEnabled(string $extension)
    {
        /** @var ExtensionManager $manager */
        $manager = resolve(ExtensionManager::class);

        return $manager->isEnabled($extension);
    }
}
