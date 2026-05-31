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
    
    public function registrar()
    {
        return view('login.novoCadastro'); 
    }

    public function salvar(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'telefone' => 'required|string|max:20'
        ]);

        $user = new \App\Models\User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->telefone = $request->input('telefone');
        $user->save();

        Auth::login($user);

        return redirect()->route('perfil.meuPerfil')->with('successo', 'Cadastro realizado com sucesso.');
    }
}
