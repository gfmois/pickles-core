<?php

namespace App\Controllers\Auth;

use App\Models\User;
use Pickles\Http\Controller;
use Pickles\Http\Request;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
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
    }
}
