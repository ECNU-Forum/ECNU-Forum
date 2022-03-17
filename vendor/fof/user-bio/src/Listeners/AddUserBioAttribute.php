<?php

/*
 * This file is part of fof/user-bio.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\UserBio\Listeners;

use Flarum\Api\Serializer\UserSerializer;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Support\Str;

class AddUserBioAttribute
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param UserSerializer $serializer
     * @param User           $user
     * @param array          $attributes
     *
     * @return array
     */
    public function __invoke(UserSerializer $serializer, User $user, array $attributes): array
    {
        $actor = $serializer->getActor();

        if ($actor->can('viewBio', $user)) {
            $attributes += [
                'bio'        => Str::limit($user->bio, $this->settings->get('fof-user-bio.maxLength'), ''),
                'canViewBio' => true,
                'canEditBio' => $actor->can('editBio', $user),
            ];
        }

        return $attributes;
    }
}
