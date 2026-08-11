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
use Illuminate\Validation\Rule;


class LoginController extends Controller
{
    public function index()
    {
        return view('login.login');
    }


    /* Processa e autentica o formulário de login */
    public function entrar(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'senha' => 'required'
        ]);


        $credenciais = [
            'email'    => $request->input('email'),
            'password' => $request->input('senha'),
            'excluido' => false
        ];

        /* Verifica se o usuário deseja permanecer logado */
        $remember = $request->has('remember');


        if (Auth::attempt($credenciais, $remember)) {
            /* Regenera o ID da sessão por prevenção */
            $request->session()->regenerate();


            if (Auth::user()->admin) {
                return redirect()->route('admin.gerenciar');
            } else {
                return redirect()->route('perfil.meuPerfil');
            }
        }
       
        return redirect()->route('login')->with('erro', 'Credenciais inválidas.');
    }


    /* Encerra a sessão do usuário */
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

    public function salvar(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'telefone' => 'nullable|string|max:20',
            'receber_avisos' => 'boolean'
        ]);


        $user = new User();
        $user->name     = $request->input('name');
        $user->email    = $request->input('email');
        $user->password = Hash::make($request->input('password')); // Hash nativo do Laravel
        $user->telefone = $request->input('telefone');
        $user->receber_avisos = $request->has('receber_avisos');
        $user->admin          = false;
        $user->excluido       = false;
        $user->save();

        Auth::login($user); //Função nativa do Laravel para logar o usuário

        return redirect()->route('perfil.meuPerfil')->with('sucesso', 'Cadastro realizado com sucesso.');
    }

    public function esqueciSenha()
    {
        return view('login.esqueciSenha');
    }

    public function enviarLinkRecuperacao(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Altere no método enviarLinkRecuperacao:
        $user = User::where('email', $request->email)->where('excluido', false)->first();

        if (!$user) {
            return redirect()->back()->with('erro', 'Não encontramos nenhum usuário com este e-mail.');
        }

        // Gera um token aleatório único e salva na tabela padrão 'password_reset_tokens' do Laravel
        $token = Str::random(60);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        // Link que será enviado por e-mail para o usuário clicar
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

    public function atualizarDados(Request $request)
    {
        $usuario = User::find(Auth::id());

        // Validação dos dados
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => ['required', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'telefone'   => 'nullable|string|max:20',
            'senha_atual' => 'nullable|required_with:nova_senha|string',
            'nova_senha'  => 'nullable|min:6|confirmed',
        ], [
            'required' => 'O campo :attribute é obrigatório.',
            'email.unique' => 'Este e-mail já está em uso por outro usuário.',
            'nova_senha.confirmed' => 'A confirmação da nova senha não confere.',
            'nova_senha.min' => 'A nova senha deve ter pelo menos 6 caracteres.',
        ]);

        // Atualiza dados cadastrais
        $usuario->name = $request->input('name');
        $usuario->email = $request->input('email');
        $usuario->telefone = $request->input('telefone');
        $usuario->receber_avisos = $request->has('receber_avisos');

        // Se informou a senha atual para alterar a senha
        if ($request->filled('nova_senha')) {
            if (!Hash::check($request->senha_atual, $usuario->password)) {
                return redirect()->back()->with('erro', 'A senha atual informada está incorreta.');
            }

            $usuario->password = Hash::make($request->nova_senha);
        }

        $usuario->save();

        return redirect()->back()->with('sucesso', 'Dados atualizados com sucesso!');
    }

    public function recuperarSenha(Request $request)
    {
        // 1. Validação dos campos vindos do formulário
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'A confirmação da nova senha não confere.',
            'password.min'       => 'A nova senha deve ter pelo menos 6 caracteres.',
        ]);

        // 2. Busca o registro do token para este e-mail
        $dadosReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        // 3. Valida se o token existe e é válido
        if (!$dadosReset || !Hash::check($request->token, $dadosReset->token)) {
            return redirect()->route('login.esqueciSenha')->with('erro', 'Token de redefinição inválido ou expirado.');
        }

        // 4. Localiza o usuário no banco
        $user = User::where('email', $request->email)->where('excluido', false)->first();

        if (!$user) {
            return redirect()->back()->with('erro', 'Usuário não encontrado.');
        }

        // 5. Atualiza a senha e remove o token usado
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // 6. Redireciona para o login com mensagem de sucesso
        return redirect()->route('login')->with('sucesso', 'Senha alterada com sucesso! Faça login com a nova senha.');
    }
}
