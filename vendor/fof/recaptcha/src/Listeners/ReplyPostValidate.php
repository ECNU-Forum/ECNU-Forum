<?php

/*
 * This file is part of fof/recaptcha.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\ReCaptcha\Listeners;

use Flarum\Post\Event\Saving;
use FoF\ReCaptcha\Validators\RecaptchaValidator;
use Illuminate\Support\Arr;

class ReplyPostValidate
{
    /**
     * @var RecaptchaValidator
     */
    protected $validator;

    /**
     * @param RecaptchaValidator $validator
     */
    public function __construct(RecaptchaValidator $validator)
    {
        $this->validator = $validator;
    }

    public function handle(Saving $event)
    {
        if (!$event->post->exists) {
            // If it's a new discussion, the reCAPTCHA is already validated in discussion saving event
            // When this code runs, the discussion already exists, and the number has not been assigned to the post yet
            // So we look in the discussion number index, just like the reply permission check does in PostReplyHandler
            if ($event->post->discussion->post_number_index === 0) {
                return;
            }

            if ($event->actor->hasPermission('fof-recaptcha.postWithoutCaptcha')) {
                return;
            }

            $this->validator->assertValid([
                'recaptcha' => Arr::get($event->data, 'attributes.g-recaptcha-response'),
            ]);
        }
    }
}
