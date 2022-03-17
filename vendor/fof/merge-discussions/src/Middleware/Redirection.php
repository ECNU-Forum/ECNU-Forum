<?php

/*
 * This file is part of fof/merge-discussions.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\MergeDiscussions\Middleware;

use FastRoute\Dispatcher\GroupCountBased;
use Flarum\Http\RouteCollection;
use FoF\MergeDiscussions\Models\Redirection as Redirect;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Redirection implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        // In case of a valid 404 response start identifying whether we need to redirect.
        if ($response instanceof Response
            && $response->getStatusCode() === 404) {

            /** @var RouteCollection $routes */
            $routes = resolve('flarum.forum.routes');

            $dispatcher = $this->getDispatcher($routes);

            // Use the route dispatcher to identify routing information.
            $route = $dispatcher->dispatch(
                $request->getMethod(),
                $request->getUri()->getPath() ?: '/'
            );

            // Identify the requested route.
            if (Arr::get($route, '1.name') !== 'discussion') {
                return $response;
            }

            // Identify the requested discussion Id.
            $id = Arr::get($route, '2.id');

            $redirect = Redirect::request($id);

            if (!$redirect) {
                return $response;
            }

            // Retrieve original URI.
            // Patch this URI with the new discussion to forward to.
            $uri = $request
                ->getUri()
                ->withPath($routes->getPath('discussion', ['id' => $redirect->to_discussion_id]));

            // Send a redirect response to the client with the predefined http code.
            return new Response\RedirectResponse(
                $uri,
                $redirect->http_code
            );
        }

        return $response;
    }

    protected function getDispatcher(RouteCollection $routes): GroupCountBased
    {
        return new GroupCountBased($routes->getRouteData());
    }
}
