<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doacao;
use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class DoacaoController extends Controller
{
    public function doacoes()
    {
        $categorias = Categoria::orderBy('nome', 'asc')->get();
        $linhas = Doacao::with('usuario')
            ->orderByRaw("
                CASE status
                    WHEN 'Em Análise' THEN 1
                    WHEN 'Aprovada' THEN 2
                    WHEN 'Integrada ao Estoque' THEN 3
                    WHEN 'Recusada' THEN 4
                    WHEN 'Cancelada' THEN 5
                    ELSE 6
                END ASC
            ")
            ->orderBy('data_doacao', 'desc')
            ->get();

        return view('admin.doacoes.doacoes', compact('linhas', 'categorias'));
    }

    public function buscar(Request $request)
    {
        $query = Doacao::with(['usuario', 'categoria']);

        if ($request->filled('termo')) {
            $termo = '%' . $request->input('termo') . '%';
            
            $query->where(function ($q) use ($termo) {
                $q->where('nome', 'ilike', $termo)
                ->orWhereHas('usuario', function ($subQuery) use ($termo) {
                    $subQuery->where('name', 'ilike', $termo);
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('retirada')) {
            if ($request->input('retirada') === 'sim') {
                $query->whereNotNull('localizacao')->where('localizacao', '!=', '');
            } elseif ($request->input('retirada') === 'nao') {
                $query->where(function ($q) {
                    $q->whereNull('localizacao')->orWhere('localizacao', '');
                });
            }
        }

        $linhas = $query->orderByRaw("
                CASE status
                    WHEN 'Em Análise' THEN 1
                    WHEN 'Aprovada' THEN 2
                    WHEN 'Integrada ao Estoque' THEN 3
                    WHEN 'Recusada' THEN 4
                    WHEN 'Cancelada' THEN 5
                    ELSE 6
                END ASC
            ")
            ->orderBy('data_doacao', 'desc')
            ->get();

        $categorias = Categoria::orderBy('nome', 'asc')->get();

        return view('admin.doacoes.doacoes', compact('linhas', 'categorias'));
    }

    public function novaDoacao()
    {
        $categorias = Categoria::orderBy('nome', 'asc')->get();
        return view('admin.doacoes.novaDoacao', compact('categorias'));
    }

    private function ajusteDados(Request $req)
    {
        $dados = $req->all();

        if ($req->hasFile('caminho_img')) {
            $imagem = $req->file('caminho_img');
            $nomeImagem = $imagem->hashName(); 
            $targetPath = public_path('img/doacoes');

            File::ensureDirectoryExists($targetPath);
            $imagem->move($targetPath, $nomeImagem);

            $dados['caminho_img'] = "img/doacoes/" . $nomeImagem;
        }

        return $dados;
    }

    private function notificarAdminsNovaDoacao(Doacao $doacao)
    {
        $admins = User::where('admin', true)->where('excluido', false)->pluck('email');

        if ($admins->isEmpty()) {
            return;
        }

        $nomeDoador = $doacao->usuario->name ?? 'Usuário';

        Mail::send([], [], function ($message) use ($admins, $doacao, $nomeDoador) {
            $message->to($admins->toArray())
                    ->subject('Nova doação recebida - Brechó')
                    ->html("<h3>Uma nova doação foi cadastrada</h3>
                            <p><strong>Item:</strong> {$doacao->nome}</p>
                            <p><strong>Doador:</strong> {$nomeDoador}</p>
                            <p>Acesse o painel administrativo para analisar.</p>");
        });
    }

    private function notificarDoadorStatus(Doacao $doacao, bool $aprovada)
    {
        $usuario = $doacao->usuario;

        if (!$usuario || !$usuario->receber_avisos) {
            return;
        }

        if ($aprovada) {
            $numeroWhatsapp = env('WHATSAPP_NUMERO', '5514991083780');
            $mensagem = rawurlencode("Olá! Tenho a doação \"{$doacao->nome}\" aprovada e gostaria de combinar a retirada.");
            $linkWhatsapp = "https://wa.me/{$numeroWhatsapp}?text={$mensagem}";

            $assunto = 'Sua doação foi aprovada! - Brechó';
            $corpo = "<h3>Boa notícia, {$usuario->name}!</h3>
                    <p>Sua doação <strong>{$doacao->nome}</strong> foi aprovada.</p>
                    <p>Para combinar a retirada, entre em contato pelo WhatsApp:</p>
                    <a href='{$linkWhatsapp}'>Falar no WhatsApp</a>";
        } else {
            $assunto = 'Sobre sua doação - Brechó';
            $corpo = "<h3>Olá, {$usuario->name}</h3>
                    <p>Infelizmente sua doação <strong>{$doacao->nome}</strong> não foi aceita desta vez.</p>
                    <p>Agradecemos muito o seu gesto e a sua vontade de contribuir!</p>
                    <p>Esperamos contar com você em futuras oportunidades. Caso queira realizar uma reserva ou fazer uma nova doação, acesse o link:</p>
                    <a href='" . route('produtos.vitrine') . "'>Acessar Vitrine</a>
                    <a href='" . route('produtos.novaDoacao') . "'>Nova Doação</a>";
        }

        Mail::send([], [], function ($message) use ($usuario, $assunto, $corpo) {
            $message->to($usuario->email)->subject($assunto)->html($corpo);
        });
    }

    public function salvar(Request $req)
    {        
        $req->validate([
            'nome'           => 'required|string',
            'categoria_nome' => 'required|string|max:255',
            'descricao'      => 'required|string',
            'caminho_img'    => 'required|image',
            'localizacao'    => $req->input('necessita_retirada') == '1' ? 'required|string' : 'nullable|string',
        ]);

        $dados = $this->ajusteDados($req);

        $categoria = Categoria::firstOrCreate([
            'nome' => Str::title(trim($req->input('categoria_nome')))
        ]);

        $dados['fk_id_categoria'] = $categoria->id_categoria;
        $dados['fk_doacao_id_usuario'] = Auth::id();
        $dados['status'] = 'Em Análise'; 

        $doacao = Doacao::create($dados);

        $this->notificarAdminsNovaDoacao($doacao);

        return redirect()->route('admin.doacoes')->with('sucesso', 'Doação cadastrada para análise!');
    }

    public function editarDoacao($id)
    {
        $linha = Doacao::findOrFail($id);
        $categorias = Categoria::orderBy('nome', 'asc')->get();

        if ($linha->status === 'Integrada ao Estoque') {
            return redirect()->route('admin.doacoes')
                ->with('erro', 'Esta doação já foi integrada e virou um produto no estoque.');
        }

        return view('admin.doacoes.editarDoacao', compact('linha', 'categorias'));
    }

    public function atualizar(Request $req, $id)
    {
        $doacao = Doacao::findOrFail($id);
        
        if ($doacao->status === 'Integrada ao Estoque') {
            return redirect()->route('admin.doacoes')
                ->with('erro', 'Não é possível alterar uma doação concluída.');
        }

        if ($req->has('status') && !$req->has('nome')) {
            $req->validate([
                'status' => 'required|in:Em Análise,Aprovada,Integrada ao Estoque,Recusada,Cancelada'
            ]);
            $dados = ['status' => $req->status];
        } else {
            $req->validate([
                'nome'           => 'required|string',
                'categoria_nome' => 'required|string|max:255',
                'descricao'      => 'required|string',
                'caminho_img'    => 'required|image',
                'localizacao'    => $req->input('necessita_retirada') == '1' ? 'required|string' : 'nullable|string',
                'status'         => 'required|in:Em Análise,Aprovada,Integrada ao Estoque,Recusada,Cancelada'
            ]);
            $dados = $this->ajusteDados($req);

            $categoria = Categoria::firstOrCreate([
                'nome' => Str::title(trim($req->input('categoria_nome')))
            ]);
            $dados['fk_id_categoria'] = $categoria->id_categoria;

            if (!$req->hasFile('caminho_img')) {
                $dados['caminho_img'] = $doacao->caminho_img;
            }
        }

        $doacao->update($dados);
        return redirect()->route('admin.doacoes')->with('sucesso', 'Doação atualizada com sucesso.');
    }

    public function integrar(Request $req, $id)
    {
        $doacao = Doacao::findOrFail($id);

        $req->validate([
            'preco' => 'required|numeric|min:0'
        ]);

        $precoDigitado = $req->input('preco'); 

        if ($doacao->status === 'Integrada ao Estoque') {
            return redirect()->route('admin.doacoes')->with('erro', 'Este item já está no estoque.');
        }

        $doacao->update(['status' => 'Integrada ao Estoque']);

        $caminhoDoacaoFisico = public_path($doacao->caminho_img); 
        $nomeArquivo = basename($doacao->caminho_img); 
        
        $caminhoNovoProdutoRelativo = "img/produtos/" . $nomeArquivo;
        $caminhoProdutoFisico = public_path($caminhoNovoProdutoRelativo); 

        if (!File::exists(public_path('img/produtos'))) {
            File::makeDirectory(public_path('img/produtos'), 0755, true);
        }
        
        if (File::exists($caminhoDoacaoFisico)) {
            File::copy($caminhoDoacaoFisico, $caminhoProdutoFisico);
        }

        Produto::create([
            'nome'             => $doacao->nome,
            'descricao'        => $doacao->descricao,
            'fk_id_categoria'  => $doacao->fk_id_categoria, 
            'caminho_img'      => $caminhoNovoProdutoRelativo, 
            'valor'            => $precoDigitado,
            'status'           => 'Disponível', 
            'excluido'         => false
        ]);

        return redirect()->route('admin.doacoes')->with('sucesso', 'Item recebido com sucesso e integrado à vitrine!');
    }

    public function aceitar($id)
    {
        $doacao = Doacao::findOrFail($id);
        $doacao->update(['status' => 'Aprovada']);

        $this->notificarDoadorStatus($doacao, aprovada: true);

        return redirect()->route('admin.doacoes')->with('sucesso', 'Doação aprovada com sucesso!');
    }

    public function rejeitar($id)
    {
        $doacao = Doacao::findOrFail($id);
        $doacao->update(['status' => 'Recusada']);

        $this->notificarDoadorStatus($doacao, aprovada: false);

        return redirect()->route('admin.doacoes')->with('sucesso', 'Doação recusada.');
    }
}