<?php

/*
 * This file is part of fof/nightmode.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\NightMode\Content;

use Flarum\Foundation\Application;
use Flarum\Frontend\Compiler\CompilerInterface;
use Flarum\Frontend\Document;
use Flarum\User\User;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface as Request;

class Assets extends \Flarum\Frontend\Content\Assets
{
    /**
     * @param Document $document
     * @param Request  $request
     */
    public function __invoke(Document $document, Request $request)
    {
        $frontend = $this->assets->getName();

        // Only apply nightmode to forum & admin frontend CSS
        if ($frontend !== 'forum' && $frontend !== 'admin') {
            parent::__invoke($document, $request);

            // Add CSS of other frontends to $document->head instead of CSS
            // so it loads after the main forum CSS.
            // E.g. fixes flarum/embed styles looking funny with nightmode installed.
            foreach ($document->css as $css) {
                $document->head[] = sprintf('<link rel="stylesheet" href="%s" />', $css);
            }
            $document->css = [];

            return;
        }

        $locale = $request->getAttribute('locale');
        $nightCss = $this->assets->makeDarkCss();
        $dayCss = $this->assets->makeCss();

        $preference = $this->getPreference($request);

        $compilers = [
            'js'  => [$this->assets->makeJs(), $this->assets->makeLocaleJs($locale)],
            'css' => [$this->assets->makeLocaleCss($locale)],
        ];

        if (resolve(Application::class)->inDebugMode()) {
            $this->commit(Arr::flatten($compilers));
            $this->commit([$dayCss, $nightCss]);
        }

        $isAuto = $preference === 0;

        if ($preference === 1 || $isAuto) {
            $document->head[] = $this->generateTag($dayCss->getUrl(), 'light', $isAuto);
        }

        if ($preference === 2 || $isAuto) {
            $document->head[] = $this->generateTag($nightCss->getUrl(), 'dark', $isAuto);
        }

        $document->js = array_merge($document->js, $this->getUrls($compilers['js']));
        $document->css = array_merge($document->css, $this->getUrls($compilers['css']));

        $document->payload['fof-nightmode.assets.day'] = $dayCss->getUrl();
        $document->payload['fof-nightmode.assets.night'] = $nightCss->getUrl();
    }

    /**
     * @param string|null $url
     * @param string      $type
     * @param string      $auto
     *
     * @return string
     */
    protected function generateTag(?string $url, string $type, string $auto)
    {
        return sprintf(
            '<link rel="stylesheet" media="%s" class="nightmode-%s" href="%s" />',
            $auto ? ($type === 'dark' ? '(prefers-color-scheme: dark)' : 'not all and (prefers-color-scheme: dark)') : '',
            $type,
            $url
        );
    }

    /**
     * @param Request $request
     *
     * @return int
     */
    protected function getPreference(Request $request)
    {
        /**
         * @var User $actor
         */
        $actor = $request->getAttribute('actor');
        $default = (int) resolve('flarum.settings')->get('fof-nightmode.default_theme');

        if ($actor->getPreference('fofNightMode_perDevice')) {
            return (int) Arr::get($request->getCookieParams(), 'flarum_nightmode', $default);
        }

        return (int) ($actor->getPreference('fofNightMode') ?? $default);
    }

    // --- original ---

    /**
     * @param array $compilers
     */
    private function commit(array $compilers)
    {
        foreach ($compilers as $compiler) {
            $compiler->commit();
        }
    }

    /**
     * @param CompilerInterface[] $compilers
     *
     * @return string[]
     */
    private function getUrls(array $compilers)
    {
        return array_filter(array_map(function (CompilerInterface $compiler) {
            return $compiler->getUrl();
        }, $compilers));
    }
}
