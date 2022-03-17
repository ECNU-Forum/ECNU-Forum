<?php

/*
 * This file is part of fof/byobu.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Byobu;

use Flarum\Api\Controller;
use Flarum\Api\Serializer;
use Flarum\Discussion\Discussion;
use Flarum\Discussion\Search\DiscussionSearcher;
use Flarum\Extend;
use Flarum\Group\Group;
use Flarum\Post\Event\Saving as PostSaving;
use Flarum\User\Event\Saving as UserSaving;
use Flarum\User\Search\UserSearcher;
use Flarum\User\User;
use FoF\Split\Events\DiscussionWasSplit;

return [
    (new Extend\Frontend('admin'))
        ->css(__DIR__.'/resources/less/admin.less')
        ->js(__DIR__.'/js/dist/admin.js'),

    (new Extend\Frontend('forum'))
        ->route('/private', 'byobuPrivate', Content\PrivateDiscussionsPage::class)
        ->css(__DIR__.'/resources/less/forum/extension.less')
        ->js(__DIR__.'/js/dist/forum.js'),

    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\Model(Discussion::class))
        ->relationship('recipientUsers', function ($discussion) {
            return $discussion->belongsToMany(User::class, 'recipients')
                ->wherePivot('removed_at', null);
        })
        ->relationship('oldRecipientUsers', function ($discussion) {
            return $discussion->belongsToMany(User::class, 'recipients')
                ->wherePivot('removed_at', '!=', null);
        })
        ->relationship('recipientGroups', function ($discussion) {
            return $discussion->belongsToMany(Group::class, 'recipients')
                ->wherePivot('removed_at', null);
        })
        ->relationship('oldRecipientGroups', function ($discussion) {
            return $discussion->belongsToMany(Group::class, 'recipients')
                ->wherePivot('removed_at', '!=', null);
        }),

    (new Extend\Model(User::class))
        ->relationship('privateDiscussions', function ($user) {
            return $user->belongsToMany(Discussion::class, 'recipients')
                ->wherePivot('removed_at', null);
        }),

    (new Extend\Model(Group::class))
        ->relationship('privateDiscussions', function ($group) {
            return $group->belongsToMany(Discussion::class, 'recipients')
                ->wherePivot('removed_at', null);
        }),

    (new Extend\ApiController(Controller\ListDiscussionsController::class))
        ->addInclude(['recipientUsers', 'recipientGroups'])
        ->load(['recipientUsers', 'recipientGroups']),

    (new Extend\ApiController(Controller\CreateDiscussionController::class))
        ->addInclude(['recipientUsers', 'recipientGroups'])
        ->load(['recipientUsers', 'recipientGroups']),

    (new Extend\ApiController(Controller\ShowDiscussionController::class))
        ->addOptionalInclude(['oldRecipientUsers', 'oldRecipientGroups'])
        ->addInclude(['recipientUsers', 'recipientGroups'])
        ->load(['recipientUsers', 'recipientGroups']),

    (new Extend\ApiSerializer(Serializer\BasicDiscussionSerializer::class))
        ->hasMany('recipientUsers', Serializer\BasicUserSerializer::class)
        ->hasMany('recipientGroups', Serializer\GroupSerializer::class),

    (new Extend\ApiSerializer(Serializer\DiscussionSerializer::class))
        ->hasMany('oldRecipientUsers', Serializer\BasicUserSerializer::class)
        ->hasMany('oldRecipientGroups', Serializer\GroupSerializer::class),

    (new Extend\ApiSerializer(Serializer\DiscussionSerializer::class))
      ->attributes(Api\DiscussionPermissionAttributes::class)
      ->attributes(Api\DiscussionDataAttributes::class),

    (new Extend\ApiSerializer(Serializer\ForumSerializer::class))
        ->attributes(Api\ForumPermissionAttributes::class),

    (new Extend\ApiSerializer(Serializer\UserSerializer::class))
        ->attribute('blocksPd', function ($serializer, $user) {
            return (bool) $user->blocks_byobu_pd;
        })
        ->attribute('cannotBeDirectMessaged', function ($serializer, $user) {
            return (bool) $serializer->getActor()->can('cannotBeDirectMessaged', $user);
        }),

    (new Extend\ApiSerializer(Serializer\CurrentUserSerializer::class))
        ->hasMany('privateDiscussions', Serializer\DiscussionSerializer::class),

    (new Extend\View())
        ->namespace('fof-byobu', __DIR__.'/resources/views'),

    (new Extend\Policy())
        ->modelPolicy(Discussion::class, Access\DiscussionPolicy::class),

    (new Extend\ModelVisibility(Discussion::class))
        ->scope(Access\ScopeDiscussionVisibility::class, 'viewPrivate'),

    (new Extend\Post())
        ->type(Posts\RecipientLeft::class)
        ->type(Posts\RecipientsModified::class)
        ->type(Posts\MadePublic::class),

    (new Extend\Notification())
        ->type(Notifications\DiscussionCreatedBlueprint::class, Serializer\DiscussionSerializer::class, ['alert', 'email'])
        ->type(Notifications\DiscussionRepliedBlueprint::class, Serializer\DiscussionSerializer::class, ['alert', 'email'])
        ->type(Notifications\DiscussionRecipientRemovedBlueprint::class, Serializer\DiscussionSerializer::class, ['alert', 'email'])
        ->type(Notifications\DiscussionAddedBlueprint::class, Serializer\DiscussionSerializer::class, ['alert', 'email'])
        ->type(Notifications\DiscussionMadePublicBlueprint::class, Serializer\DiscussionSerializer::class, ['alert']),

    (new Extend\Event())
        ->listen(PostSaving::class, Listeners\IgnoreApprovals::class)
        ->listen(UserSaving::class, Listeners\SaveUserPreferences::class)
        ->listen(DiscussionWasSplit::class, Listeners\AddRecipientsToSplitDiscussion::class)
        ->subscribe(Listeners\CreatePostWhenRecipientsChanged::class)
        ->subscribe(Listeners\QueueNotificationJobs::class),

    (new Extend\ServiceProvider())
        ->register(Provider\ByobuProvider::class),

    (new Extend\ModelPrivate(Discussion::class))
        ->checker(Listeners\GetModelIsPrivate::class),

    (new Extend\SimpleFlarumSearch(DiscussionSearcher::class))
        ->addGambit(Gambits\Discussion\ByobuGambit::class)
        ->addGambit(Gambits\Discussion\PrivacyGambit::class),

    (new Extend\SimpleFlarumSearch(UserSearcher::class))
        ->addGambit(Gambits\User\AllowsPdGambit::class),

    (new Extend\Settings())
        // we have to use the callback here, else we risk returning empty values instead of the defaults.
        // see https://github.com/flarum/core/issues/3209
        ->serializeToForum('byobu.icon-badge', 'fof-byobu.icon-badge', function ($value): string {
            return empty($value) ? 'fas fa-map' : $value;
        })
        ->serializeToForum('byobu.icon-postAction', 'fof-byobu.icon-postAction', function ($value): string {
            return empty($value) ? 'far fa-map' : $value;
        }),
];
