<?php

namespace App\Http\Controllers;
use App\Models\Produto;

class InstitucionalController
{
    public function index()
    {
        $produtos = Produto::where('status', 'Disponível')
                            ->where('excluido', false) // Evita exibir os deletados logicamente
                            ->take(8) // Limita a quantidade para encaixar no layout horizontal
                            ->get();

        return view('institucional.index', compact('produtos'));
    }

    public function quemSomos()
    {
        return view('institucional.quemSomos');
    }
}
