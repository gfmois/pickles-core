<?php

namespace Pickles\View;

/**
 * Interface Engine
 *
 * Defines the contract for a view rendering engine.
 * Any implementing class must provide a `render` method capable of rendering a view
 * with optional parameters and layout support.
 */
interface Engine
{
    /**
     * Renders a view with optional layout and dynamic parameters.
     *
     * @param string      $view   The name of the view file to render (without the .php extension).
     * @param array       $params An associative array of variables to be extracted and made available to the view.
     * @param string|null $layout Optional name of the layout file to wrap the view. If null, a default may be used.
     *
     * @return string The fully rendered output as a string.
     */
    public function render(string $view, array $params = [], ?string $layout = null): string;
}
