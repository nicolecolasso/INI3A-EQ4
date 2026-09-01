<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user && $user->excluido) {
                return redirect()->route('login')->with('erro', 'Esta conta está desativada.');
            }

            if (!$user) {
                $user = User::create([
                    'name'            => $googleUser->getName(),
                    'email'           => $googleUser->getEmail(),
                    'password'        => bcrypt(str()->random(16)),
                    'telefone'        => null,
                    'admin'           => false,
                    'excluido'        => false,
                    'receber_avisos'  => false,
                ]);
            }

            Auth::login($user);

            if ($user->admin) {
                return redirect()->route('admin.gerenciar');
            }

            return redirect()->route('institucional.index');
        } catch (Exception $e) {
            return redirect()->route('login')->with('erro', 'Falha ao autenticar com o Google.');
        }
    }
}