<?php

namespace App\Providers;

use Pickles\Providers\ServiceProvider;
use Pickles\Validation\Rule;

class RuleServiceProvider implements ServiceProvider
{
    public function registerServices()
    {
        Rule::loadDefaults();
    }
}
