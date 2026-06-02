<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Exibe a visão que pede usuário e senha (GET /login)
     */
    public function index() 
    {
        return view('login.login');
    }

    /**
     * Processa e autentica o formulário de login (POST /login)
     */
    public function entrar(Request $request) 
    {
        // Validação rápida para garantir o preenchimento dos campos nativos
        $request->validate([
            'email' => 'required|email',
            'senha' => 'required'
        ]);

        // Mapeia o campo 'senha' vindo do formulário HTML nativo para o padrão 'password' do Laravel
        $credenciais = [
            'email'    => $request->input('email'),
            'password' => $request->input('senha') 
        ];

        // Tenta realizar a autenticação
        if (Auth::attempt($credenciais)) {
            
            // Regra de segurança recomendada: Regenera o ID da sessão após o login
            $request->session()->regenerate();

            // Verifica se o usuário autenticado possui a coluna 'admin' ativa no banco
            if (Auth::user()->admin) {
                return redirect()->route('admin.gerenciar');
            } else {
                return redirect()->route('perfil.meuPerfil');
            }
        } 
        
        // Se as credenciais estiverem erradas, volta para a tela de login enviando a mensagem de erro
        return redirect()->route('login')->with('erro', 'Credenciais inválidas.');
    }

    /**
     * Encerra a sessão do usuário (GET /login/sair)
     */
    public function sair(Request $request) 
    { 
        Auth::logout();

        // Invalida a sessão atual e gera um novo token CSRF por segurança
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('institucional.index');
    } 
    
    /**
     * Exibe a tela de novo cadastro
     */
    public function registrar()
    {
        return view('login.novoCadastro'); 
    }

    /**
     * Salva o novo usuário no banco de dados
     */
    public function salvar(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'telefone' => 'nullable|string|max:20'
        ]);

        // Criação usando a classe User importada corretamente no topo
        $user = new User();
        $user->name     = $request->input('name');
        $user->email    = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->telefone = $request->input('telefone');
        $user->save();

        // Faz o login automático do usuário recém-criado
        Auth::login($user);

        return redirect()->route('perfil.meuPerfil')->with('sucesso', 'Cadastro realizado com sucesso.');
    }
}