<?php

return [
    "boot" => [
        Pickles\Providers\ServerServiceProvider::class,
        Pickles\Providers\DatabaseServiceProvider::class,
        Pickles\Providers\SessionStorageServiceProvider::class,
        Pickles\Providers\ViewServiceProvider::class,
        Pickles\Providers\AuthenticatorServiceProvider::class
    ],
    "runtime" => [
        App\Providers\RuleServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ],
];