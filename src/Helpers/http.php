<?php

use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Kernel;

/**
 * Returns a JSON response with the given data.
 *
 * @param array $data The data to be encoded as JSON and returned in the response.
 * @return Response The JSON response object.
 */
function json(array $data): Response
{
    return Response::json($data);
}

/**
 * Redirects the user to the specified URI.
 *
 * This function generates a redirect response to the given URI.
 *
 * @param string $uri The URI to redirect to.
 * @return Response The redirect response object.
 */
function redirect(string $uri): Response
{
    return Response::redirect($uri);
}

/**
 * Renders a view and returns it as a HTTP response.
 *
 * @param string $view The name of the view file to render.
 * @param array $data An associative array of data to pass to the view.
 * @param string|null $layout Optional layout to wrap the view content.
 * @return Response The HTTP response containing the rendered view.
 */
function view(string $view, array $data = [], ?string $layout = null): Response
{
    return Response::view($view, $data, $layout);
}

/**
 * Retrieves the current HTTP request instance.
 *
 * This function fetches the current request object from the application kernel.
 * If the application instance is not of type `Kernel`, an exception is thrown.
 *
 * @throws \Exception If the application instance is not a `Kernel`.
 *
 * @return Request The current HTTP request instance.
 */
function request(): Request
{
    $appInstance = app();
    if (!$appInstance instanceof Kernel) {
        throw new \Exception('Kernel instance not found.');
    }

    return $appInstance->getRequest();
}

/**
 * Redirects the user to the previous request URL stored in the session.
 * If no previous URL is found, it defaults to the root ("/").
 *
 * @return Response The HTTP response object for the redirection.
 */
function back(): Response
{
    return redirect(session()->get(Constants::PREVIOUS_REQUEST_KEY, "/"));
}
