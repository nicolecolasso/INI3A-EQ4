<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\User;

class LoginController extends Controller
{
    /* Exibe a visão que pede usuário e senha (GET /login)*/
    public function index() 
    {
        return view('login.login');
    }

    /* Processa e autentica o formulário de login (POST /login)*/
    public function entrar(Request $request) 
    {
        $request->validate([
            'email' => 'required|email',
            'senha' => 'required'
        ]);

        $credenciais = [
            'email'    => $request->input('email'),
            'password' => $request->input('senha') 
        ];

        $remember = $request->has('remember');

        if (Auth::attempt($credenciais, $remember)) {
            $request->session()->regenerate();

            if (Auth::user()->admin) {
                return redirect()->route('admin.gerenciar');
            } else {
                return redirect()->route('perfil.meuPerfil');
            }
        } 
        
        return redirect()->route('login')->with('erro', 'Credenciais inválidas.');
    }

    /* Encerra a sessão do usuário (GET /login/sair) */
    public function sair(Request $request) 
    { 
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('institucional.index');
    } 
    

    public function registrar()
    {
        return view('login.novoCadastro'); 
    }

    /**
     * Salva o novo usuário no banco de dados (POST /novo-cadastro)
     */
    public function salvar(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'telefone' => 'nullable|string|max:20'
        ]);

        $user = new User();
        $user->name     = $request->input('name');
        $user->email    = $request->input('email');
        $user->password = Hash::make($request->input('password')); // Hash seguro nativo do Laravel
        $user->telefone = $request->input('telefone');
        $user->save();

        Auth::login($user);

        return redirect()->route('perfil.meuPerfil')->with('sucesso', 'Cadastro realizado com sucesso.');
    }


    public function esqueciSenha()
    {
        return view('login.esqueciSenha');
    }

    /**
     * Processa o pedido de recuperação e envia o e-mail (POST /recuperar-senha)
     */
    public function enviarLinkRecuperacao(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()->with('erro', 'Não encontramos nenhum usuário com este e-mail.');
        }

        // Gera um token aleatório único e salva na tabela padrão 'password_reset_tokens' do Laravel
        $token = Str::random(60);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        // Monta o link que será enviado por e-mail para o usuário clicar
        $link = route('password.reset', ['token' => $token, 'email' => $request->email]);

        // Dispara o e-mail (precisa configurar o .env com os dados do servidor que será passado nos próximos bimestres com as informações SMTP)
        Mail::send([], [], function ($message) use ($request, $link) {
            $message->to($request->email)
                    ->subject('Recuperação de Senha - Brechó')
                    ->html("<h3>Você solicitou a alteração de senha</h3><p>Clique no link abaixo para redefinir sua senha:</p><a href='{$link}'>Redefinir Minha Senha</a>");
        });

        return redirect()->back()->with('status', 'Enviamos um e-mail com o link de recuperação de senha!');
    }


    public function mostrarTelaRecuperarSenha(Request $request, $token)
    {
        // Pega o e-mail que veio junto na URL e injeta na view para o form saber quem alterar
        return view('login.recuperarSenha', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Valida os campos e altera de fato a senha no banco (POST /atualizar-senha)
     */
    public function atualizarSenha(Request $request)
    {
        // O Laravel valida automaticamente se 'password' é igual a 'password_confirmation' usando o 'confirmed'
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed', // 'confirmed' obriga o campo password_confirmation existir e ser idêntico
        ]);

        // Busca o registro do token na tabela auxiliar do Laravel
        $registroToken = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        // Verifica se o token existe e bate com o hash salvo
        if (!$registroToken || !Hash::check($request->token, $registroToken->token)) {
            return redirect()->route('login')->with('erro', 'Este link de recuperação é inválido ou expirou.');
        }

        // Atualiza a senha do usuário
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Deleta o token para ele não ser usado de novo por segurança
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('sucesso', 'Sua senha foi alterada com sucesso! Faça login.');
    }
}