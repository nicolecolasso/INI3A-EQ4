<?php

namespace App\Http\Controllers;
use App\Models\Produto;
use \App\Models\Banner;

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
}
