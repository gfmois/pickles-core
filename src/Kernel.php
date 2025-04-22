<?php

namespace Pickles;

use Constants;
use Pickles\Http\HttpMethod;
use Pickles\Http\HttpNotFoundException;
use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Routing\Router;
use Pickles\Server\PhpNativeServer;
use Pickles\Server\Server;
use Pickles\Session\PhpNativeSessionStorage;
use Pickles\Session\Session;
use Pickles\Validation\Exceptions\ValidationException;
use Pickles\Validation\Rule;
use Pickles\View\Engine;
use Pickles\View\PicklesEngine;
use ReflectionClass;
use Throwable;

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
     * The session instance used to manage user sessions.
     *
     * @var Session
     */
    public Session $session;

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
        $instance->session = new Session(new PhpNativeSessionStorage());
        Rule::loadDefaults();

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
            $this->terminate($this->getRouter()->resolve($this->request));
        } catch (HttpNotFoundException $e) {
            $this->abort(Response::text("Not Found")->setStatus(404));
        } catch (ValidationException $e) {
            $this->abort(back()->withErrors($e->getErrors(), 422));
        } catch (Throwable $e) {
            $response = [
                "error" => (new ReflectionClass($e))->getShortName(),
                "message" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ];

            $this->abort(
                json($response)->setStatus(500)
            );
        }
    }

    /**
     * Get the value of viewEngine
     */
    public function getViewEngine()
    {
        return $this->viewEngine;
    }

    /**
     * Get the value of session
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }

    public function prepareNextRequest()
    {
        if ($this->request->getMethod() === HttpMethod::GET) {
            $this->session->set(Constants::PREVIOUS_REQUEST_KEY, $this->request->getUri());
        }
    }

    public function terminate(Response $response)
    {
        $this->prepareNextRequest();
        $this->server->sendResponse($response);
    }

    public function abort(Response $response): void
    {
        $this->terminate($response);
    }
}
