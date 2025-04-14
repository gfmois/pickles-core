<?php

namespace Pickles\View;

use Pickles\View\Engine;

class PicklesEngine implements Engine
{
    protected string $viewsDir;
    protected string $defaultLayout = "main";
    protected string $contentAnnotation = "@content";

    public function __construct(string $viewsDir)
    {
        $this->viewsDir = $viewsDir;
    }

    public function render(string $view, array $params = [], ?string $layout = null): string
    {
        $this->validateViewFile($this->viewsDir, $view);

        $layoutContent = $this->renderLayout($layout ?? $this->defaultLayout);
        $viewContent = $this->renderView($view, $params);

        return str_replace($this->contentAnnotation, $viewContent, $layoutContent);
    }

    protected function renderLayout(string $layout): string
    {
        return $this->getPhpFileOutput("{$this->viewsDir}/layouts/{$layout}.php");
    }

    protected function renderView(string $view, array $params = []): string
    {
        return $this->getPhpFileOutput("{$this->viewsDir}/{$view}.php", $params);
    }

    protected function getPhpFileOutput(string $phpFile, array $params = []): string
    {
        foreach ($params as $param => $value) {
            $$param = $value;
        }

        ob_start();
        include_once $phpFile;
        return ob_get_clean();
    }

    private function validateViewFile(string $viewsDir, string $view): void
    {
        $phpFile = "{$viewsDir}/{$view}.php";
        if (!file_exists($phpFile)) {
            throw new FileNotFoundException("File {$view}.php not found on {$viewsDir} directory.");
        }

        if (empty($view)) {
            throw new FileNotFoundException("Empty string passed to be render.");
        }
    }

    /**
     * Set the value of defaultLayout
     *
     * @return  self
     */
    public function setDefaultLayout($defaultLayout)
    {
        $this->defaultLayout = $defaultLayout;

        return $this;
    }

    /**
     * Set the value of viewsDir
     *
     * @return  self
     */
    public function setViewsDir($viewsDir)
    {
        $this->viewsDir = $viewsDir;

        return $this;
    }
}
