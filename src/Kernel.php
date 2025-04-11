<?php

namespace Pickles;

use Pickles\Container\Container;
use Pickles\Http\HttpNotFoundException;
use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Routing\Router;
use Pickles\Server\PhpNativeServer;
use Pickles\Server\Server;

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
    
    /**
     * The request instance representing the current HTTP request.
     *
     * @var Request
     */
    
    /**
     * The server instance providing server-related utilities and information.
     *
     * @var Server
     */
    public Router $router;
    public Request $request;
    public Server $server;

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
        $instance = Container::singleton(self::class);
        $instance->router = new Router();
        $instance->server = new PhpNativeServer();
        $instance->request = $instance->server->getRequest();

        return $instance;
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
            $route = $this->router->resolve($this->request);
            $this->request->setRoute($route);
            $action = $route->getAction();
            $response = $action($this->request);
            $this->server->sendResponse($response);
        } catch (HttpNotFoundException $e) {
            $response = Response::text("Not Found")->setStatus(404);
            $this->server->sendResponse($response);
        }
    }
}
