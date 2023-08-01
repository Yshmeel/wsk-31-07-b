<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Organizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login() {
        return view('login');
    }

    public function postLogin(LoginRequest $request) {
        $email = $request->get('email');
        $password = $request->get('password');

        $organizer = Organizer::query()->where('email', $email)->first();

        if($organizer == null || !Hash::check($password, $organizer->password_hash)) {
            return redirect('/login')->with('error', 'Email or password not correct');
        }

        Auth::login($organizer);
        return redirect('/events');
    }

    public function logout() {
        Auth::logout();
        return redirect('/login');
    }
}
