<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'checkAdmin']], function () {
    Route::get('gerenciar', [
        'as'   => 'admin.gerenciar',
        'uses' => 'App\Http\Controllers\Admin\AdminController@gerenciar'
    ]);

    //Doações - Painel Administrativo
    Route::get('doacoes/doacoes', [
        'as'   => 'admin.doacoes',
        'uses' => 'App\Http\Controllers\Admin\DoacaoController@doacoes'
    ]);

    Route::get('doacoes/novaDoacao', [
        'as'   => 'admin.doacoes.novaDoacao',
        'uses' => 'App\Http\Controllers\Admin\DoacaoController@novaDoacao'
    ]);

    Route::post('doacoes/salvar', [
        'as'   => 'admin.doacoes.salvar',
        'uses' => 'App\Http\Controllers\Admin\DoacaoController@salvar'
    ]);

    Route::get('doacoes/editarDoacao/{id}', [
        'as'   => 'admin.doacoes.editarDoacao',
        'uses' => 'App\Http\Controllers\Admin\DoacaoController@editarDoacao'
    ]);

    Route::put('doacoes/atualizar/{id}', [
        'as'   => 'admin.doacoes.atualizar',
        'uses' => 'App\Http\Controllers\Admin\DoacaoController@atualizar'
    ]);


    //Produtos - Painel Administrativo
    Route::get('produtos/produtos', [
        'as'   => 'admin.produtos',
        'uses' => 'App\Http\Controllers\Admin\ProdutoController@produtos'
    ]);

    Route::get('produtos/novoProduto', [
        'as'   => 'admin.produtos.novoProduto',
        'uses' => 'App\Http\Controllers\Admin\ProdutoController@novoProduto'
    ]);

    Route::post('produtos/salvar', [
        'as'   => 'admin.produtos.salvar',
        'uses' => 'App\Http\Controllers\Admin\ProdutoController@salvar'
    ]);

    Route::get('produtos/editarProduto/{id}', [
        'as'   => 'admin.produtos.editarProduto',
        'uses' => 'App\Http\Controllers\Admin\ProdutoController@editarProduto'
    ]);

    Route::put('produtos/atualizar/{id}', [
        'as'   => 'admin.produtos.atualizar',
        'uses' => 'App\Http\Controllers\Admin\ProdutoController@atualizar'
    ]);

    Route::get('produtos/excluir/{id}', [
        'as'   => 'admin.produtos.excluir',
        'uses' => 'App\Http\Controllers\Admin\ProdutoController@excluir'
    ]);

    //Reserva - Painel Administrativo
    Route::get('reservas/reservas', [
        'as'   => 'admin.reservas',
        'uses' => 'App\Http\Controllers\Admin\ReservaController@reservas'
    ]);

    Route::get('reservas/novaReserva', [
        'as'   => 'admin.reservas.novaReserva',
        'uses' => 'App\Http\Controllers\Admin\ReservaController@novaReserva'
    ]);

    Route::post('reservas/salvar', [
        'as'   => 'admin.reservas.salvar',
        'uses' => 'App\Http\Controllers\Admin\ReservaController@salvar'
    ]);

    Route::get('reservas/editarReserva/{id}', [
        'as'   => 'admin.reservas.editarReserva',
        'uses' => 'App\Http\Controllers\Admin\ReservaController@editarReserva'
    ]);

    Route::put('reservas/atualizar/{id}', [
        'as'   => 'admin.reservas.atualizar',
        'uses' => 'App\Http\Controllers\Admin\ReservaController@atualizar'
    ]);



    //Usuário - Painel Administrativo
    Route::get('usuarios/usuarios', [
        'as'   => 'admin.usuarios',
        'uses' => 'App\Http\Controllers\Admin\UsuarioController@usuarios'
    ]);

    Route::get('usuarios/novoUsuario', [
        'as'   => 'admin.usuarios.novoUsuario',
        'uses' => 'App\Http\Controllers\Admin\UsuarioController@novoUsuario'
    ]);

    Route::post('usuarios/salvar', [
        'as'   => 'admin.usuarios.salvar',
        'uses' => 'App\Http\Controllers\Admin\UsuarioController@salvar'
    ]);

    Route::get('usuarios/editarUsuario/{id}', [
        'as'   => 'admin.usuarios.editarUsuario',
        'uses' => 'App\Http\Controllers\Admin\UsuarioController@editarUsuario'
    ]);

    Route::put('usuarios/atualizar/{id}', [
        'as'   => 'admin.usuarios.atualizar',
        'uses' => 'App\Http\Controllers\Admin\UsuarioController@atualizar'
    ]);

    Route::get('usuarios/excluir/{id}', [
        'as'   => 'admin.usuarios.excluir',
        'uses' => 'App\Http\Controllers\Admin\UsuarioController@excluir'
    ]);


    //Comunicados - Painel Administrativo
    Route::get('comunicados/comunicados', [
        'as'   => 'admin.comunicados',
        'uses' => 'App\Http\Controllers\Admin\ComunicadoController@comunicados'
    ]);

    Route::get('comunicados/novo', [
        'as'   => 'admin.comunicados.novoComunicado',
        'uses' => 'App\Http\Controllers\Admin\ComunicadoController@novoComunicado'
    ]);

    Route::post('comunicados/salvar', [
        'as'   => 'admin.comunicados.salvar',
        'uses' => 'App\Http\Controllers\Admin\ComunicadoController@salvar'
    ]);

    Route::get('comunicados/reenviar/{id}', [
        'as'   => 'admin.comunicados.reenviarComunicado',
        'uses' => 'App\Http\Controllers\Admin\ComunicadoController@reenviarComunicado'
    ]);
});