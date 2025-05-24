<?php

return [
    /**
     * The "boot" array lists service provider classes that will be registered
     * and bootstrapped during the application's startup process.
     * 
     * Each provider is responsible for initializing and configuring a specific
     * part of the application's infrastructure, such as server handling,
     * database connections, session storage, view rendering, authentication,
     * and password hashing.
     *
     * @var array List of fully qualified service provider class names to be booted.
     */
    "boot" => [
        Pickles\Providers\ServerServiceProvider::class,
        Pickles\Providers\DatabaseServiceProvider::class,
        Pickles\Providers\SessionStorageServiceProvider::class,
        Pickles\Providers\ViewServiceProvider::class,
        Pickles\Providers\AuthenticatorServiceProvider::class,
        Pickles\Providers\HasherServiceProvider::class,
    ],
    
    /**
     * The "runtime" array lists service provider classes that will be registered
     * and bootstrapped during the application's runtime.
     * 
     * These providers are typically used for dynamic features that may change
     * during the application's execution, such as routing and rule management.
     *
     * @var array List of fully qualified service provider class names to be registered at runtime.
     */
    "runtime" => [
        App\Providers\RuleServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\AppServiceProvider::class,
    ],

    /**
     * The "cli" array lists service provider classes that will be registered
     * and bootstrapped when the application is run from the command line interface (CLI).
     * 
     * These providers are typically used for CLI-specific features, such as database
     * management or command-line utilities.
     *
     * @var array List of fully qualified service provider class names to be registered for CLI usage.
     */
    'cli' => [
        Pickles\Providers\DatabaseServiceProvider::class,
    ]
];