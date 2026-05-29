<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Compra;

class CompraController extends Controller
{
    public function reservas()
    {
        return view('admin.reservas.reservas');
    }

    public function listaReservas()
    {
        $linhas = Compra::all();
        return view('admin.reservas.listaReservas', compact('linhas'));
    }

    public function novaReserva()
    {
        return view('admin.reservas.novaReserva');
    }

    public function salvar(Request $req)
    {
        $dados = $req->all();
        
        $dados['fk_compra_id_produto'] = $dados['id_produto'];
        $dados['fk_compra_id_usuario'] = $dados['id_usuario'];
        $dados['data_reserva'] = now();

        Compra::create($dados);
        
        return redirect()->route('admin.reservas');
    }

    public function editarReserva($id)
    {
        $linha = Compra::find($id);
        return view('admin.reservas.editarReserva', compact('linha'));
    }

    public function atualizar(Request $req, $id)
    {
        Compra::find($id)->update($req->all());
        return redirect()->route('admin.reservas');
    }

}
