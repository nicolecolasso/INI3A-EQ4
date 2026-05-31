<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function index() { // a visão que pede usuário e senha
        return view('login.login');
    }

    public function autenticar(Request $request) {

        $credenciais = [
            'email'    => $request->input('email'),
            'password' => $request->input('senha') 
        ];

        if (Auth::attempt($credenciais)) {
            if (Auth::user()->admin) {
                return redirect()->route('admin.gerenciar');
            } else {
                return redirect()->route('perfil.meuPerfil');
            }
        } else {
            return redirect()->route('login.login')->with('erro', 'Credenciais inválidas.');
        }
    }

    public function logout() { 
        Auth::logout();
        return redirect()->route('institucional.index');
    }  
}
