<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Jobs\SendVerificationEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function register(RegisterRequest $request)
    {
        // Создание пользователя
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Назначение роли пользователя
        $user->assignRole('user');

        // Постановка задачи в очередь
        SendVerificationEmail::dispatch($user);

        // Редирект на страницу входа с сообщением об успешной регистрации
        return redirect('/login')
            ->with('success', 'Registration successful. Please check your email for verification.');
    }

    public function verifyEmail(Request $request)
    {
        // Валидация временной подписи
        if (! $request->hasValidSignature()) {
            return redirect('/login')->withErrors(['message' => 'Invalid or expired verification link']);
        }

        $user = User::find($request->route('id'));

        if (! $user) {
            return redirect('/login')->withErrors(['message' => 'User not found']);
        }

        // Проверка совпадения хеша email
        if (! hash_equals((string) $request->route('hash'), sha1($user->email))) {
            return redirect('/login')->withErrors(['message' => 'Invalid hash']);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/login')->with('message', 'Email already verified');
        }

        $user->markEmailAsVerified();

        // Аутентификация пользователя с remember_token
        auth()->loginUsingId($user->id, true);

        // Перенаправление пользователя на index
        return redirect('/');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('auth.showLoginForm');
    }
}
