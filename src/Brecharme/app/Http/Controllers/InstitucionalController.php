<?php

namespace App\Http\Controllers;
use App\Models\Produto;
use \App\Models\Banner;
use \App\Models\Galeria;
use \App\Models\InstagramDestaque;

class InstitucionalController
{
    public function index()
    {
        $banners = Banner::orderBy('ordem')->get();
        $produtos = Produto::where('status', 'Disponível')
                            ->where('excluido', false) 
                            ->take(8) // Limita a quantidade para encaixar no layout horizontal
                            ->get();

        return view('institucional.index', compact('banners', 'produtos'));
    }

    public function quemSomos()
    {
        return view('institucional.quemSomos');
    }

    public function galeria()
    {
        $postsInstagram = InstagramDestaque::orderBy('created_at', 'desc')->get();
        $fotosGaleria = Galeria::orderBy('created_at', 'desc')->get();
        return view('institucional.galeria', compact('postsInstagram', 'fotosGaleria'));
    }
}
