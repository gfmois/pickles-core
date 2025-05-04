<?php

namespace Pickles;

use Constants;
use Pickles\Database\Drivers\DatabaseDriver;
use Pickles\Database\Drivers\PdoDriver;
use Pickles\Database\Model;
use Pickles\Http\HttpMethod;
use Pickles\Http\Exceptions\HttpNotFoundException;
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

    public DatabaseDriver $database;

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
        $instance->database = singleton(DatabaseDriver::class, PdoDriver::class);
        ;
        $instance->database->connect("mysql", "127.0.0.1", 3306, "root", "1234", "pickles");

        Model::setDatabaseDriver($instance->database);
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

    /**
     * Prepares the application for the next request.
     *
     * If the current request method is GET, this method stores the URI of the
     * current request in the session under a predefined key. This allows the
     * application to keep track of the previous request's URI for future use.
     *
     * @return void
     */
    public function prepareNextRequest()
    {
        if ($this->request->getMethod() === HttpMethod::GET) {
            $this->session->set(Constants::PREVIOUS_REQUEST_KEY, $this->request->getUri());
        }
    }

    /**
     * Terminates the current request lifecycle.
     *
     * This method performs cleanup tasks after the response has been sent.
     * It prepares the system for the next request, sends the response to the client,
     * closes the database connection, and terminates the script execution.
     *
     * @param Response $response The response object to be sent to the client.
     * @return void
     */
    public function terminate(Response $response)
    {
        $this->prepareNextRequest();
        $this->server->sendResponse($response);
        $this->database->close();
        exit(0);
    }

    /**
     * Aborts the current process by terminating it with the given response.
     *
     * @param Response $response The response object used to terminate the process.
     *
     * @return void
     */
    public function abort(Response $response): void
    {
        $this->terminate($response);
    }
}
