<?php

use App\Controllers\Auth\RegisterController;
use App\Models\User;
use Pickles\Crypto\Hasher;
use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Routing\Route;

Route::get("/", fn(Request $request) => Response::text(auth()?->name ?? "Guest"));
Route::get("/form", fn(Request $request) => view("form"));

Route::get("/register", [RegisterController::class, 'view' ]);
Route::post("/register", [RegisterController::class, 'create' ]);

Route::get("/login", fn(Request $request) => view("auth/login"));
Route::post("/login", function (Request $request) {
    $data = $request->validate([
        "email" => ["required", "email"],
        "password" => ["required", "min:6"],
    ]);

    $user = User::firstWhere("email", $data["email"]);
    $hasherInstance = app(Hasher::class);
    if (!$hasherInstance instanceof Hasher) {
        throw new \RuntimeException("The hasher is not an instance of Pickles\Crypto\Hasher.");
    }

    if ($user && $hasherInstance->verify($data["password"], $user->password)) {
        $user->login();
        return redirect("/");
    }

    return back()->withErrors(["email" => ["Invalid email or password."]]);
});

Route::get("/logout", function (Request $request) {
    auth()->logout();
    return redirect("/");
});
