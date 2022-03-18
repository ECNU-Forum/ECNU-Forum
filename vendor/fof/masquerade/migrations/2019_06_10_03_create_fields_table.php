<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if ($schema->hasTable('fof_masquerade_fields')) {
            return;
        }
        // This migration includes the changes made through multiple migrations in the Flagrow version (up to v0.2.1)
        $schema->create('fof_masquerade_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('required')->default(false)->index();
            $table->string('validation')->nullable();
            $table->string('prefix')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort')->nullable()->index();
            $table->boolean('on_bio')->default(false);
            $table->string('type')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('fof_masquerade_fields');
    }
];
