<?php

use App\Models\User;
use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Routing\Route;

Route::get("/", fn(Request $request) => Response::text(auth()->name));
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
