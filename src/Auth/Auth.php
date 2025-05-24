<?php

namespace Pickles\Auth;

use Pickles\Auth\Authenticators\Authenticator;
use Pickles\Container\Exceptions\InvalidInstanceSavedException;
use App\Controllers\Auth\LoginController;
use App\Controllers\Auth\RegisterController;
use App\Models\User;
use Pickles\Http\Response;
use Pickles\Routing\Route;

class Auth
{
    public static function user(): ?Authenticatable
    {
        $authenticatorInstance = app(Authenticator::class);
        if (!$authenticatorInstance instanceof Authenticator) {
            throw new InvalidInstanceSavedException();
        }

        return $authenticatorInstance->resolve();
    }

    public static function isGuest(): bool
    {
        return self::user() === null;
    }

    public static function routes()
    {
        Route::get("/", fn () => Response::text(auth()?->name ?? "Guest"));
        Route::get("/form", fn () => view("form"));
        Route::get("/user/{user}", fn (User $user) => json($user->toArray()));

        Route::get("/register", [RegisterController::class, 'view']);
        Route::post("/register", [RegisterController::class, 'create']);

        Route::get("/login", [LoginController::class, 'view']);
        Route::post("/login", [LoginController::class, 'login']);

        Route::get("/logout", [LoginController::class, 'logout']);
    }
}
