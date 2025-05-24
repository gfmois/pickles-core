<?php

namespace App\Controllers\Auth;

use App\Models\User;
use Pickles\Crypto\Hasher;
use Pickles\Http\Request;

class LoginController
{
    public function view()
    {
        return view("auth/login");
    }

    public function login(Request $request, Hasher $hasher)
    {
        $data = $request->validate([
            "email" => ["required", "email"],
            "password" => ["required", "min:6"],
        ]);

        $user = User::firstWhere("email", $data["email"]);

        if ($hasher === null || !($hasher instanceof Hasher)) {
            throw new \RuntimeException("The hasher is not an instance of Pickles\Crypto\Hasher.");
        }

        if ($user && $hasher->verify($data["password"], $user->password)) {
            $user->login();
            return redirect("/");
        }

        return back()->withErrors(["email" => ["Invalid email or password."]]);
    }

    public function logout()
    {
        auth()->logout();
        return redirect("/");
    }
}
