<?php

/*
 * This file is part of fof/recaptcha.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        /**
         * @var $settings SettingsRepositoryInterface
         */
        $settings = resolve(SettingsRepositoryInterface::class);

        if ($value = $settings->get($key = 'sijad-recaptcha.sitekey')) {
            $settings->set('fof-recaptcha.credentials.site', $value);
            $settings->delete($key);
        }

        if ($value = $settings->get($key = 'sijad-recaptcha.secret')) {
            $settings->set('fof-recaptcha.credentials.secret', $value);
            $settings->delete($key);
        }
    },
];
