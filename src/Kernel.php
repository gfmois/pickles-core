<?php

namespace Pickles;

use Pickles\Http\HttpNotFoundException;
use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Routing\Router;
use Pickles\Server\PhpNativeServer;
use Pickles\Server\Server;
use Pickles\View\Engine;
use Pickles\View\PicklesEngine;

/**
 * Class Kernel
 *
 * The Kernel class is the core of the Pickles framework. It is responsible for
 * bootstrapping the application, handling HTTP requests, resolving routes,
 * executing actions, and sending HTTP responses.
 *
 * @package Pickles
 */
class Kernel
{
    /**
     * The router instance responsible for handling route definitions and dispatching.
     *
     * @var Router
     */
    public Router $router;

    /**
     * The request instance representing the current HTTP request.
     *
     * @var Request
     */
    public Request $request;

    /**
     * The server instance providing server-related utilities and information.
     *
     * @var Server
     */
    public Server $server;

    /**
     * The view engine instance used for rendering views.
     *
     * @var Engine
     */
    public Engine $viewEngine;

    /**
     * Bootstraps the application by initializing and configuring core components.
     *
     * Creates a singleton instance of the Kernel class, sets up the
     * router, server, and request objects, and returns the initialized instance.
     *
     * @return self Returns the singleton instance of the Kernel class.
     */
    public static function bootstrap()
    {
        $instance = singleton(self::class);
        $instance->router = new Router();
        $instance->server = new PhpNativeServer();
        $instance->request = $instance->server->getRequest();
        $instance->viewEngine = new PicklesEngine(__DIR__ . "/../views");

        return $instance;
    }

    /**
     * Get the server instance providing server-related utilities and information.
     *
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Get the value of request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the value of server
     *
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Executes the main application logic by resolving the route,
     * invoking the corresponding action, and sending the response.
     *
     * @return void
     */
    public function run()
    {
        try {
            $response = $this->getRouter()->resolve($this->request);
            $this->server->sendResponse($response);
        } catch (HttpNotFoundException $e) {
            $response = Response::text("Not Found")->setStatus(404);
            $this->server->sendResponse($response);
        }
    }

    /**
     * Get the value of viewEngine
     */
    public function getViewEngine()
    {
        return $this->viewEngine;
    }
}
