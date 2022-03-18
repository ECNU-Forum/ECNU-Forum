<?php

namespace FoF\Masquerade;

use Flarum\Api\Serializer\UserSerializer;
use Flarum\User\User;

class UserAttributes
{
    public function __invoke(UserSerializer $serializer, User $user, array $attributes): array
    {
        $actor = $serializer->getActor();

        if ($actor->id === $user->id) {
            // Own profile
            $attributes['canEditMasqueradeProfile'] = $actor->can('fof.masquerade.have-profile');
        } else {
            // Other's profile
            $attributes['canEditMasqueradeProfile'] = $actor->can('fof.masquerade.edit-others-profile');
        }

        return $attributes;
    }
}
