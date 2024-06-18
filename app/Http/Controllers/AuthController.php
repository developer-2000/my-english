<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            return redirect('/');
        }

        return redirect()->back()->withErrors(['email' => 'Login or password is incorrect']);
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}

