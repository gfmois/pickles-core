<?php

namespace Pickles\Providers;

use Constants;
use Pickles\View\Engine;
use Pickles\View\PicklesEngine;

class ViewServiceProvider implements ServiceProvider
{
    public function registerServices()
    {
        match (config(Constants::VIEW_ENGINE, Constants::DEFAULT_VIEW_ENGINE)) {
            Constants::DEFAULT_VIEW_ENGINE => singleton(Engine::class, fn () => new PicklesEngine(config(Constants::VIEW_PATH)))
        };
    }
}
