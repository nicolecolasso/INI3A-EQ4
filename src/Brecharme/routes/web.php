<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

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

    Route::get('doacoes/aprovar/{id}', [
        'as'   => 'admin.doacoes.aprovar',
        'uses' => 'App\Http\Controllers\Admin\DoacaoController@aprovar'
    ]);

    Route::get('doacoes/rejeitar/{id}', [
        'as'   => 'admin.doacoes.rejeitar',
        'uses' => 'App\Http\Controllers\Admin\DoacaoController@rejeitar'
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
        'uses' => 'App\Http\Controllers\Admin\CompraController@reservas'
    ]);

    Route::get('reservas/novaReserva', [
        'as'   => 'admin.reservas.novaReserva',
        'uses' => 'App\Http\Controllers\Admin\CompraController@novaReserva'
    ]);

    Route::post('reservas/salvar', [
        'as'   => 'admin.reservas.salvar',
        'uses' => 'App\Http\Controllers\Admin\CompraController@salvar'
    ]);

    Route::get('reservas/editarReserva/{id}', [
        'as'   => 'admin.reservas.editarReserva',
        'uses' => 'App\Http\Controllers\Admin\CompraController@editarReserva'
    ]);

    Route::put('reservas/atualizar/{id}', [
        'as'   => 'admin.reservas.atualizar',
        'uses' => 'App\Http\Controllers\Admin\CompraController@atualizar'
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
    Route::get('comunicados/novo', [
        'as'   => 'admin.comunicados.novoComunicado',
        'uses' => 'App\Http\Controllers\Admin\ComunicadoController@novoComunicado'
    ]);

    Route::post('comunicados/salvar', [
        'as'   => 'admin.comunicados.salvar',
        'uses' => 'App\Http\Controllers\Admin\ComunicadoController@salvar'
    ]);

    Route::get('comunicados/reenviar', [
        'as'   => 'admin.comunicados.reenviarComunicado',
        'uses' => 'App\Http\Controllers\Admin\ComunicadoController@reenviarComunicado'
    ]);
});

Route::group(['prefix' => 'login'], function () {
    // 1. Tela de Login (GET /login) -> Nomeada como 'login'
    Route::get('/', [LoginController::class, 'index'])->name('login');

    // 2. Processar o Login (POST /login) -> Alterada a URL para '/' para bater com o formulário!
    Route::post('/', [LoginController::class, 'entrar'])->name('login.entrar');

    // 3. Deslogar do sistema (GET /login/sair)
    Route::get('/sair', [LoginController::class, 'sair'])->name('login.sair');

    Route::get('/novoCadastro', [
        'as'   => 'login.novoCadastro',
        'uses' => 'App\Http\Controllers\LoginController@registrar'
    ]);

    Route::post('/salvarCadastro', [
        'as'   => 'login.salvarCadastro',
        'uses' => 'App\Http\Controllers\LoginController@salvar'
    ]);
});

Route::group(['prefix' => 'produtos'], function () {
    Route::get('/vitrine', [
        'as' => 'produtos.vitrine',
        'uses' => 'App\Http\Controllers\VitrineController@vitrine'
    ]);

    Route::get('/detalheProduto/{id}', [
        'as' => 'produtos.detalheProduto',
        'uses' => 'App\Http\Controllers\VitrineController@detalheProduto'
    ]);

    Route::group(['middleware' => ['auth']], function () {
        Route::get('/novaDoacao', [
            'as' => 'produtos.novaDoacao',
            'uses' => 'App\Http\Controllers\VitrineController@novaDoacao'
        ]);
        Route::post('/salvarDoacao', [
            'as' => 'produtos.salvarDoacao',
            'uses' => 'App\Http\Controllers\VitrineController@salvarDoacao'
        ]);
    });
});

Route::group(['prefix' => 'carrinho', 'middleware' => ['auth']], function () {
    Route::get('/', [
        'as' => 'carrinho',
        'uses' => 'App\Http\Controllers\CarrinhoController@index'
    ]);

    Route::post('/adicionar/{id}', [
        'as' => 'carrinho.adicionar',
        'uses' => 'App\Http\Controllers\CarrinhoController@adicionar'
    ]);

    Route::post('/finalizar/{id_compra}', [
        'as' => 'carrinho.finalizar',
        'uses' => 'App\Http\Controllers\CarrinhoController@finalizar'
    ]);

    Route::get('/conclusaoReserva/{id_usuario}', [
        'as' => 'carrinho.conclusaoReserva',
        'uses' => 'App\Http\Controllers\CarrinhoController@conclusaoReserva'
    ]);
});

Route::group(['prefix' => 'perfil', 'middleware' => ['auth']], function () {
    Route::get('/meuPerfil', [
        'as' => 'perfil.meuPerfil',
        'uses' => 'App\Http\Controllers\PerfilController@meuPerfil'
    ]);

    Route::get('/meusDados', [
        'as' => 'perfil.meusDados',
        'uses' => 'App\Http\Controllers\PerfilController@meusDados'
    ]);

    Route::get('/minhasDoacoes', [
        'as' => 'perfil.minhasDoacoes',
        'uses' => 'App\Http\Controllers\PerfilController@minhasDoacoes'
    ]);

    Route::get('/minhasReservas', [
        'as' => 'perfil.minhasReservas',
        'uses' => 'App\Http\Controllers\PerfilController@minhasReservas'
    ]);

    Route::get('/minhasDoacoes/cancelar/{id}', [
        'as'   => 'perfil.minhasDoacoes.cancelar',
        'uses' => 'App\Http\Controllers\PerfilController@cancelarDoacao'
    ]);

    Route::get('/minhasReservas/cancelar/{id}', [
        'as'   => 'perfil.minhasReservas.cancelar',
        'uses' => 'App\Http\Controllers\PerfilController@cancelarReserva'
    ]);
});

Route::get('/', [
    'as' => 'institucional.index',
    'uses' => 'App\Http\Controllers\InstitucionalController@index'
]);
Route::get('/quemSomos', [
    'as' => 'institucional.quemSomos',
    'uses' => 'App\Http\Controllers\InstitucionalController@quemSomos'
]);