<?php

/*
 * This file is part of fof/secure-https.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\SecureHttps;

use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use s9e\TextFormatter\Renderer;
use s9e\TextFormatter\Utils;

class FormatImages
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var UrlGenerator
     */
    protected $url;

    public function __construct(SettingsRepositoryInterface $settings, UrlGenerator $url)
    {
        $this->settings = $settings;
        $this->url = $url;
    }

    /**
     * Configure rendering for user mentions.
     *
     * @param Renderer    $renderer
     * @param mixed       $context
     * @param string|null $xml
     *
     * @return string $xml to be unparsed
     */
    public function __invoke(Renderer $renderer, $context, string $xml)
    {
        if ((bool) $this->settings->get('fof-secure-https.proxy', false)) {
            $xml = Utils::replaceAttributes($xml, 'IMG', function ($attributes) {
                $attributes['src'] = $this->url->to('api')->route('fof.secure-https.imgurl').'?imgurl='.urlencode($attributes['src']);

                return $attributes;
            });
        }

        return $xml;
    }
}
