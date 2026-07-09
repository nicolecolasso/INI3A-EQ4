<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Galeria;
use App\Models\InstagramDestaque;
use Illuminate\Support\Facades\File;

class GaleriaController extends Controller
{
    public function index()
    {
        $postsInstagram = InstagramDestaque::orderBy('created_at', 'desc')->get();
        $fotosGaleria = Galeria::orderBy('created_at', 'desc')->get();

        return view('admin.galeria', compact('postsInstagram', 'fotosGaleria'));
    }

    public function salvarInsta(Request $request)
    {
        $request->validate(['link_post' => 'required|url']);

        InstagramDestaque::create([
            'link_post' => $request->link_post
        ]);

        return redirect()->back()->with('sucesso', 'Link do Instagram vinculado com sucesso!');
    }

    public function excluirInsta($id)
    {
        $post = InstagramDestaque::findOrFail($id);
        $post->delete();

        return redirect()->back()->with('sucesso', 'Destaque do Instagram removido!');
    }

    public function salvarFoto(Request $request)
    {
        $request->validate([
            'caminho_img' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
            'titulo_evento' => 'nullable|string|max:100'
        ]);

        $dados = [
            'titulo_evento' => $request->titulo_evento
        ];

        if ($request->hasFile('caminho_img')) {
            $imagem = $request->file('caminho_img');
            $nomeImagem = $imagem->hashName(); 
            $targetPath = public_path('img/bazar');

            File::ensureDirectoryExists($targetPath);
            $imagem->move($targetPath, $nomeImagem);

            $dados['caminho_img'] = "img/bazar/" . $nomeImagem;
        }

        Galeria::create($dados);

        return redirect()->back()->with('sucesso', 'Foto adicionada à galeria local!');
    }

    public function excluirFoto($id)
    {
        $foto = Galeria::findOrFail($id);

        if ($foto->caminho_img) {
            $arquivoPath = public_path($foto->caminho_img);
            if (File::exists($arquivoPath)) {
                File::delete($arquivoPath);
            }
        }

        $foto->delete();

        return redirect()->back()->with('sucesso', 'Foto removida da galeria!');
    }
}
