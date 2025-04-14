<?php

namespace Pickles\View;

interface Engine
{
    public function render(string $view, array $params = [], ?string $layout = null): string;
}
