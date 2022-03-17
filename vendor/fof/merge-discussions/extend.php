<?php

/*
 * This file is part of fof/merge-discussions.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\MergeDiscussions;

use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Database\AbstractModel;
use Flarum\Extend;
use FoF\MergeDiscussions\Events\DiscussionWasMerged;
use FoF\MergeDiscussions\Posts\DiscussionMergePost;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),
    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\Routes('api'))
        ->get('/discussions/{id}/merge', 'fof.merge-discussions.preview', Api\Controllers\MergePreviewController::class)
        ->post('/discussions/{id}/merge', 'fof.merge-discussions.run', Api\Controllers\MergeController::class)
        ->remove('discussions.show')
        ->get('/discussions/{id}', 'discussions.show', Api\Controllers\ShowDiscussionByNumberController::class),

    (new Extend\Post())
        ->type(DiscussionMergePost::class),

    (new Extend\Event())
        ->listen(DiscussionWasMerged::class, Listeners\CreatePostWhenMerged::class)
        ->listen(DiscussionWasMerged::class, Listeners\NotifyParticipantsWhenMerged::class),

    (new Extend\ApiSerializer(DiscussionSerializer::class))
        ->attribute('canMerge', function (DiscussionSerializer $serializer, AbstractModel $discussion) {
            return $serializer->getActor()->can('merge', $discussion);
        }),

    (new Extend\Settings())
        ->serializeToForum('fof-merge-discussions.search_limit', 'fof-merge-discussions.search_limit', 'intVal', 4),

    (new Extend\View())
        ->namespace('fof-merge-discussions', __DIR__.'/resources/views'),

    (new Extend\Notification())
        ->type(Notification\DiscussionMergedBlueprint::class, DiscussionSerializer::class, ['alert', 'email']),

    (new Extend\Middleware('forum'))
        ->insertBefore(HandleErrors::class, Middleware\Redirection::class),
];
