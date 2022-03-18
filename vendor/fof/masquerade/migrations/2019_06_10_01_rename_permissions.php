<?php

use Flarum\Group\Permission;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        Permission::query()
            ->where('permission', 'flagrow.masquerade.view-profile')
            ->update(['permission' => 'fof.masquerade.view-profile']);
        Permission::query()
            ->where('permission', 'flagrow.masquerade.have-profile')
            ->update(['permission' => 'fof.masquerade.have-profile']);
    },
    'down' => function (Builder $schema) {
        // Not doing anything but `down` has to be defined
    },
];
