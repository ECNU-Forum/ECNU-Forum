<?php

/*
 * This file is part of fof/byobu.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Byobu\Api;

use Flarum\Api\Serializer\ForumSerializer;

class ForumPermissionAttributes
{
    /**
     * @param Flarum\Api\Serializer\ForumSerializer $serializer
     * @param mixed                                 $model
     * @param array                                 $attributes
     *
     * @return mixed
     */
    public function __invoke(ForumSerializer $serializer, $model, array $attributes)
    {
        $actor = $serializer->getActor();
        $users = $actor->can('discussion.startPrivateDiscussionWithUsers');
        $groups = $actor->can('discussion.startPrivateDiscussionWithGroups');

        $attributes['canStartPrivateDiscussion'] = $users || $groups;
        $attributes['canStartPrivateDiscussionWithUsers'] = $users;
        $attributes['canStartPrivateDiscussionWithGroups'] = $groups;
        $attributes['canStartPrivateDiscussionWithBlockers'] = $actor->can('discussion.startPrivateDiscussionWithBlockers');

        return $attributes;
    }
}
