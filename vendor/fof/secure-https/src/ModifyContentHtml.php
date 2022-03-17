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

use Flarum\Api\Serializer\BasicPostSerializer;
use Flarum\Post\Post;
use Flarum\Settings\SettingsRepositoryInterface;

class ModifyContentHtml
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    protected $regex = '/<img src="http:\/\/(.+?)" title="(.*?)" alt="(.*?)">/';

    protected $subst = '<img onerror="$(this).next().empty().append(\'<blockquote style=&#92;&#39;background-color: #c0392b; color: white;&#92;&#39; class=&#92;&#39;uncited&#92;&#39;><div><p>\'+app.translator.trans(\'fof-secure-https.forum.removed\')+\' | <a href=&#92;&#39;http://$1&#92;&#39; style=&#92;&#39;color:white;&#92;&#39;target=&#92;&#39;_blank&#92;&#39;>\'+app.translator.trans(\'fof-secure-https.forum.show\')+\'</a></p></div></blockquote>\');$(this).hide();" onload="$(this).next().empty();" class="securehttps-replaced" src="https://$1" title="$2" alt="$3"><span><i class="icon fa fa-spinner fa-spin"></i> &nbsp;Loading Image</span>';

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function __invoke(BasicPostSerializer $serializer, Post $post, array $attributes): array
    {
        if (!(bool) $this->settings->get('fof-secure-https.proxy', false) && isset($attributes['contentHtml'])) {
            $attributes['contentHtml'] = preg_replace($this->regex, $this->subst, $attributes['contentHtml']);
        }

        return $attributes;
    }
}
