<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Notifications\ResetPassword;

use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status'    =>  'success',
            'message'   =>  'Usuario creado con exito',
            'user'      =>  $user,
            'authorization' => [
                'token' =>  $token,
                'type'  =>  'bearer',
            ]
        ]);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sin autorizacion'
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'status'    =>  'success',
            'user'      =>  $user,
            'authorization' => [
                'token' =>  $token,
                'type'  =>  'bearer',
            ]
        ]);
    }

    public function forgot(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        ResetPassword::createUrlUsing(function ($user, string $token) {
            $front = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173'));
            return $front . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
        });

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => 'ok', 'message' => __($status)], 200)
            : response()->json(['message' => __($status)], 400);
    }

    public function reset_password(Request $request)
    {
        $request->validate([
            'token'     => 'required',
            'email'     => 'required|email',
            'password'  => 'required|min:8|confirmed',
        ]);

        $resetUser = null;

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) use (&$resetUser) {
                $user->forceFill(['password' => Hash::make($password)])
                    ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
                $resetUser = $user;
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)], 400);
        }

        $token = Auth::login($resetUser);
        return response()->json([
            'status' => 'success',
            'user' => $resetUser,
            'authorization' => ['token' => $token, 'type' => 'bearer'],
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Sesion cerrada con exito'
        ]);
    }


}
