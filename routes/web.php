<?php

use App\Models\User;
use Pickles\Crypto\Hasher;
use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Routing\Route;

Route::get("/", fn(Request $request) => Response::text(auth()?->name ?? "Guest"));
Route::get("/form", fn(Request $request) => view("form"));

Route::get("/register", fn(Request $request) => view("auth/register"));
Route::post("/register", function (Request $request) {
    $data = $request->validate([
        "email" => ["required", "email"],
        "name" => ["required"],
        "password" => ["required", "min:8"],
        "confirm_password" => ["required"]
    ]);

    if ($data["password"] !== $data["confirm_password"]) {
        return back()->withErrors(["confirm_password" => ["confirm" => "Password and Confirm Password do not match."]]);
    }

    $data["password"] = hashString($data["password"]);
    $user = User::create($data);
    $user->login();

    return redirect("/");
});

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
