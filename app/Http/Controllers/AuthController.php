<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $key = 'login|'.$request->ip().'|'.$request->input('email');


        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);


            return response()->json([
                'message' => "Demasiados intentos. Inténtalo de nuevo en $seconds segundos."
            ], 429);
        }


        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            RateLimiter::clear($key);


            $user = Auth::user();


            $token = $user->createToken('admin_token')->plainTextToken;


            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'token' => $token,
                'user' => $user
            ], 200);
        }


        RateLimiter::hit($key);


        return response()->json([
            'message' => 'Las credenciales no coinciden.'
        ], 401);
    }

    public function logout(Request $request)
    {

        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Sesión cerrada correctamente.'
        ], 200);
    }
}
