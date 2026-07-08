<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index(){
        $banners = Banner::all()->keyBy('ordem');
        return view('admin.banners', compact('banners'));
    }

    public function update(Request $request, $ordem)
    {
        $request->validate([
            'caminho_img' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $banner = Banner::firstOrNew(['ordem' => $ordem]);

        // Se já existia um banner cadastrado no banco, remove o arquivo físico antigo
        if ($banner->caminho_img) {
            $antigoPath = public_path($banner->caminho_img);
            if (File::exists($antigoPath) && !str_contains($banner->caminho_img, 'brecharme')){
                File::delete($antigoPath);
            }
        }

        // Upload seguindo o método padrão
        if ($request->hasFile('caminho_img')) {
            $imagem = $request->file('caminho_img');
            $nomeImagem = $imagem->hashName(); 
            $targetPath = public_path('img/banners');

            File::ensureDirectoryExists($targetPath);
            $imagem->move($targetPath, $nomeImagem);

            // Grava o caminho relativo correto no banco
            $banner->caminho_img = "img/banners/" . $nomeImagem;
        }

        $banner->save();

        return redirect()->back()->with('sucesso', "Banner da posição {$ordem} atualizado com sucesso!");
    }
}
