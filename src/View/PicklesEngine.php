<?php

namespace Pickles\View;

use Pickles\View\Engine;

class PicklesEngine implements Engine {
    protected string $viewsDir;

    public function __construct(string $viewsDir) {
        $this->viewsDir = $viewsDir;
    }

    public function render(string $view): string {
        $phpFile = "{$this->viewsDir}/{$view}.php";
        if (!file_exists($phpFile)) {
            throw new FileNotFoundException("File {$view}.php not found on {$this->viewsDir} directory.");
        }

        if (empty($view)) {
            throw new FileNotFoundException("Empty string passed to be render.");
        }

        ob_start();
        include_once $phpFile;
        return ob_get_clean();
    }
}
