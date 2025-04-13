<?php

namespace Pickles\View;

interface Engine {
    public function render(string $view): string;
}