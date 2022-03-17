<?php

/*
 * This file is part of fof/recaptcha.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\ReCaptcha\Content;

use Flarum\Frontend\Document;
use Flarum\Locale\Translator;

class AddRecaptchaJs
{
    protected $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function __invoke(Document $document)
    {
        $locale = $this->translator->getLocale();

        $document->head[] = "<script src=\"https://www.recaptcha.net/recaptcha/api.js?hl=$locale&render=explicit\" async defer></script>";
    }
}
