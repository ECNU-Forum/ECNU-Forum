<?php

/*
 * This file is part of fof/byobu.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Byobu\Gambits\Discussion;

use Flarum\Http\SlugManager;
use Flarum\Search\AbstractRegexGambit;
use Flarum\Search\SearchState;
use Flarum\User\User;
use FoF\Byobu\Database\RecipientsConstraint;

/**
 * Filters results to discussions that include the given user as recipient. Used to show private discussions on a user profile.
 */
class ByobuGambit extends AbstractRegexGambit
{
    use RecipientsConstraint;

    /**
     * @var SlugManager
     */
    protected $slugManager;

    /**
     * @param SlugManager $slugManager
     */
    public function __construct(SlugManager $slugManager)
    {
        $this->slugManager = $slugManager;
    }

    protected function getGambitPattern(): string
    {
        return 'byobu:(.+)';
    }

    protected function conditions(SearchState $search, array $matches, $negate)
    {
        $user = $this->slugManager->forResource(User::class)->fromSlug(trim($matches[1], '"'), $search->getActor());

        $search->getQuery()->where(function ($query) use ($user) {
            $this->forRecipient($query, [], $user->id);
        });
    }
}
