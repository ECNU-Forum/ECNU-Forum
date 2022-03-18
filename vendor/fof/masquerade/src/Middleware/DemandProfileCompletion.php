<?php

namespace FoF\Masquerade\Middleware;

use Flarum\Http\RequestUtil;
use Flarum\Http\SlugManager;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use FoF\Masquerade\Repositories\FieldRepository;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DemandProfileCompletion implements MiddlewareInterface
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;
    /**
     * @var FieldRepository
     */
    protected $fields;
    /**
     * @var UrlGenerator
     */
    protected $url;
    /**
     * @var SlugManager
     */
    protected $slugManager;

    public function __construct(SettingsRepositoryInterface $settings, FieldRepository $fields, UrlGenerator $url, SlugManager $slugManager)
    {
        $this->settings = $settings;
        $this->fields = $fields;
        $this->url = $url;
        $this->slugManager = $slugManager;
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $actor = RequestUtil::getActor($request);

        if (!$actor->isGuest()) {

            $configureProfileUrl = $this->url->to('forum')->route('user', [
                'username' => $this->slugManager->forResource(User::class)->toSlug($actor),
                'filter' => 'masquerade'
            ]);

            $configureProfilePathWithoutBase = str_replace($this->url->to('forum')->base(), '', $configureProfileUrl);

            if (
                $configureProfilePathWithoutBase !== $request->getUri()->getPath() &&
                $actor->can('fof.masquerade.have-profile') &&
                $this->settings->get('masquerade.force-profile-completion') &&
                !$this->fields->completed($actor->id)
            ) {
                return new RedirectResponse($configureProfileUrl);
            }
        }

        return $handler->handle($request);
    }
}
