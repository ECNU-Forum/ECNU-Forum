<?php

use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        // Re-use the tables from the Flagrow version if they exist
        if ($schema->hasTable('flagrow_masquerade_fields') && !$schema->hasTable('fof_masquerade_fields')) {
            $schema->rename('flagrow_masquerade_fields', 'fof_masquerade_fields');
        }
        if ($schema->hasTable('flagrow_masquerade_answers') && !$schema->hasTable('fof_masquerade_answers')) {
            $schema->rename('flagrow_masquerade_answers', 'fof_masquerade_answers');
        }
    },
    'down' => function (Builder $schema) {
        // Not doing anything but `down` has to be defined
    },
];
