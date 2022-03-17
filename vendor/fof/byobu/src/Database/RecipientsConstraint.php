<?php

/*
 * This file is part of fof/byobu.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Byobu\Database;

use Flarum\Flags\Flag;
use Flarum\User\User;
use FoF\Byobu\Concerns\ExtensionsDiscovery;
use Illuminate\Database\Eloquent\Builder as Eloquent;
use Illuminate\Database\Query\Builder as Query;

trait RecipientsConstraint
{
    use ExtensionsDiscovery;

    /**
     * @param Query|Eloquent $query
     * @param User           $user
     * @param bool           $unify
     */
    public function constraint($query, User $user, bool $includeFlagged = true)
    {
        if ($user->isGuest()) {
            return;
        }

        $query
            // Do a subquery where for filtering.
            ->orWhere(function ($query) use ($user, $includeFlagged) {
                // Open access for is_private discussions when the user is
                // part of the recipients either directly or through a group.
                $this->forRecipient($query, $user->groups->pluck('id')->all(), $user->id);

                // Open access for is_private discussions when the user handles
                // flags and any of the posts inside the discussion is flagged.
                if (
                    $this->flagsInstalled()
                    && $user->hasPermission('user.viewPrivateDiscussionsWhenFlagged')
                    && $user->hasPermission('discussion.viewFlags')
                    && $includeFlagged
                ) {
                    $this->whenFlagged($query);
                }
            });
    }

    /**
     * @param Query|Eloquent $query
     * @param array          $groupIds
     * @param int            $userId
     */
    protected function forRecipient($query, array $groupIds, int $userId)
    {
        $query->whereIn('discussions.id', function ($query) use ($groupIds, $userId) {
            $query->select('recipients.discussion_id')
                ->from('recipients')
                ->whereNull('recipients.removed_at')
                ->where(function ($query) use ($groupIds, $userId) {
                    $query
                        ->select('recipients.discussion_id')
                        ->where('recipients.user_id', $userId)
                        ->when(!empty($groupIds), function ($query) use ($groupIds) {
                            $query->orWhereIn('recipients.group_id', $groupIds);
                        })->distinct();
                })->distinct();
        });
    }

    protected function whenFlagged($query)
    {
        // In case posts have been flagged, open them up..
        $query->orWhere(function ($query) {
            // .. but only if they have recipients (are private discussions)
            $query
                ->whereIn('discussions.id', function ($query) {
                    $query
                        ->select('recipients.discussion_id')
                        ->from('recipients')
                        ->whereNull('recipients.removed_at');
                    // .. and only if any of the contained posts are flagged
                })
                ->whereIn('discussions.id', function ($query) {
                    $query
                        ->select('posts.discussion_id')
                        ->from('posts')
                        ->whereIn('posts.id', function ($query) {
                            $query->select('flags.post_id')
                                ->from('flags')
                                ->distinct();
                        })
                        ->distinct();
                });
        });
    }

    protected function flagsInstalled(): bool
    {
        return $this->extensionIsEnabled('flarum-flags') && class_exists(Flag::class);
    }
}
