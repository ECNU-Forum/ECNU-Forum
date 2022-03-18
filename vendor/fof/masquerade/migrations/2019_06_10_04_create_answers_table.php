<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if ($schema->hasTable('fof_masquerade_answers')) {
            return;
        }
        // This migration includes the changes made through multiple migrations in the Flagrow version (up to v0.2.1)
        $schema->create('fof_masquerade_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('field_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->text('content')->nullable();
            $table->timestamps();

            $table->unique(['field_id', 'user_id']);

            $table->foreign('field_id')
                ->references('id')
                ->on('fof_masquerade_fields')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('fof_masquerade_answers');
    }
];
