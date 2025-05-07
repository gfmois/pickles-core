<?php

namespace Pickles;

use Constants;
use Dotenv\Dotenv;
use Pickles\Config\Config;
use Pickles\Database\Drivers\DatabaseDriver;
use Pickles\Database\Model;
use Pickles\Http\HttpMethod;
use Pickles\Http\Exceptions\HttpNotFoundException;
use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Providers\Exceptions\InvalidServiceProviderException;
use Pickles\Providers\ServiceProvider;
use Pickles\Routing\Router;
use Pickles\Server\Server;
use Pickles\Session\Session;
use Pickles\Session\SessionStorage;
use Pickles\Validation\Exceptions\ValidationException;
use Pickles\View\Engine;
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
    public static string $root;
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
     * Bootstraps the application by initializing the root directory,
     * loading configuration, running service providers, setting up HTTP handlers,
     * establishing a database connection, and running runtime service providers.
     *
     * @param string $root The root directory of the application.
     * @return self Returns the instance of the Kernel after bootstrapping.
     */
    public static function bootstrap(string $root)
    {
        self::$root = $root;
        $instance = singleton(self::class);

        return $instance
            ->loadConfig()
            ->runServiceProviders(Constants::BOOT_PROVIDERS)
            ->setHttpHandlers()
            ->setUpDatabaseConnection()
            ->runServiceProviders(Constants::RUNTIME_PROVIDERS);
    }

    /**
     * Loads the application configuration.
     *
     * This method initializes the environment variables using the Dotenv library
     * and loads the application configuration files from the specified directory.
     *
     * @return self Returns the current instance for method chaining.
     */
    protected function loadConfig(): self
    {
        Dotenv::createImmutable(self::$root)->load();
        Config::load(self::$root . "/config");

        return $this;
    }

    /**
     * Executes the service providers of the specified type.
     *
     * This method iterates through the list of service providers defined in the configuration
     * for the given type, instantiates each provider, and ensures it implements the
     * ServiceProvider interface. If a provider does not implement the required interface,
     * an InvalidServiceProviderException is thrown. Each valid provider's `registerServices`
     * method is then called.
     *
     * @param string $type The type of service providers to execute ("boot" or "runtime").
     * @return self Returns the current instance for method chaining.
     * @throws InvalidServiceProviderException If a provider does not implement the ServiceProvider interface.
     */
    protected function runServiceProviders(string $type): self
    {
        foreach (config("providers.$type", []) as $provider) {
            $provider = new $provider();
            if (!$provider instanceof ServiceProvider) {
                throw new InvalidServiceProviderException("ServiceProvider $provider does not implements ServiceProvider.");
            }

            $provider->registerServices();
        }

        return $this;
    }

    /**
     * Sets up the HTTP handlers for the application.
     *
     * This method initializes and assigns the following components:
     * - Router: A singleton instance of the Router class.
     * - Server: An application instance of the Server class.
     * - Request: The current HTTP request obtained from the server.
     * - Session: A singleton instance of the Session class, initialized with a
     *   SessionStorage instance.
     *
     * @return self Returns the current instance for method chaining.
     */
    protected function setHttpHandlers(): self
    {
        $this->router = singleton(Router::class);
        $this->server = app(Server::class);
        $this->request = $this->server->getRequest();
        $this->session = singleton(Session::class, fn () => new Session(app(SessionStorage::class)));

        return $this;
    }

    /**
     * Sets up the database connection for the application.
     *
     * This method initializes the database driver and establishes a connection
     * using configuration values such as protocol, host, port, username, password,
     * and database name. It also sets the database driver for the application's
     * models to ensure consistent database interactions.
     *
     * @return self Returns the current instance for method chaining.
     */
    protected function setUpDatabaseConnection(): self
    {
        $this->database = app(DatabaseDriver::class);

        $this->database->connect(
            config(Constants::DATABASE_PROTOCOL),
            config(Constants::DATABASE_HOST),
            config(Constants::DATABASE_PORT),
            config(Constants::DATABASE_USERNAME),
            config(Constants::DATABASE_PASSWORD),
            config(Constants::DATABASE_DATABASE),
        );
        Model::setDatabaseDriver($this->database);

        return $this;
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
