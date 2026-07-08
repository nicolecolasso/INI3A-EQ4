<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'checkAdmin']], function () {
    Route::get('gerenciar', [
        'as'   => 'admin.gerenciar',
        'uses' => 'App\Http\Controllers\Admin\AdminController@gerenciar'
    ]);

    // Banners - Painel Administrativo

    Route::get('/banners', [
       'as'   => 'admin.banners',
        'uses' => 'App\Http\Controllers\Admin\BannerController@index'
    ]);

    Route::post('/banners/update/{ordem}', [
        'as'   => 'admin.banners.update',
        'uses' => 'App\Http\Controllers\Admin\BannerController@update'
    ]);

    // Doações - Painel Administrativo
    Route::get('doacoes/doacoes', [
        'as'   => 'admin.doacoes',
        'uses' => 'App\Http\Controllers\Admin\DoacaoController@doacoes'
    ]);

    Route::get('doacoes/buscar', [
        'as'   => 'admin.doacoes.buscar',
        'uses' => 'App\Http\Controllers\Admin\DoacaoController@buscar'
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

    // Deixamos explicitamente PUT para o formulário e POST para ações rápidas de status
    Route::put('doacoes/atualizar/{id}', [
        'as'   => 'admin.doacoes.atualizar',
        'uses' => 'App\Http\Controllers\Admin\DoacaoController@atualizar'
    ]);

    Route::post('doacoes/integrar/{id}', [
        'as'   => 'admin.doacoes.integrar',
        'uses' => 'App\Http\Controllers\Admin\DoacaoController@integrar'
    ]);

    Route::post('doacoes/rejeitar/{id}', [
        'as'   => 'admin.doacoes.rejeitar',
        'uses' => 'App\Http\Controllers\Admin\DoacaoController@rejeitar'
    ]);

    Route::post('doacoes/aceitar/{id}', [
        'as'   => 'admin.doacoes.aceitar',
        'uses' => 'App\Http\Controllers\Admin\DoacaoController@aceitar'
    ]);


    // Produtos - Painel Administrativo
    Route::get('produtos/produtos', [
        'as'   => 'admin.produtos',
        'uses' => 'App\Http\Controllers\Admin\ProdutoController@produtos'
    ]);

    Route::get('produtos/buscar', [
        'as'   => 'admin.produtos.buscar',
        'uses' => 'App\Http\Controllers\Admin\ProdutoController@buscar'
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

    Route::get('produtos/ativar/{id}', [
        'as'   => 'admin.produtos.ativar',
        'uses' => 'App\Http\Controllers\Admin\ProdutoController@ativar'
    ]);

    // Reserva - Painel Administrativo
    Route::get('reservas/reservas', [
        'as'   => 'admin.reservas',
        'uses' => 'App\Http\Controllers\Admin\CompraController@reservas'
    ]);

    Route::get('reservas/buscar', [
        'as'   => 'admin.reservas.buscar',
        'uses' => 'App\Http\Controllers\Admin\CompraController@buscar'
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


    // Usuário - Painel Administrativo
    Route::get('usuarios/usuarios', [
        'as'   => 'admin.usuarios',
        'uses' => 'App\Http\Controllers\Admin\UsuarioController@usuarios'
    ]);

    Route::get('usuarios/buscar', [
        'as'   => 'admin.usuarios.buscar',
        'uses' => 'App\Http\Controllers\Admin\UsuarioController@buscar'
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

    Route::get('usuarios/ativar/{id}', [
        'as'   => 'admin.usuarios.ativar',
        'uses' => 'App\Http\Controllers\Admin\UsuarioController@ativar'
    ]);


    // Comunicados - Painel Administrativo
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
    Route::get('/', [
        'as' => 'login', 
        'uses' => 'App\Http\Controllers\LoginController@index'
        ]);
    Route::post('/', [
        'as' => 'login.entrar', 
        'uses' => 'App\Http\Controllers\LoginController@entrar'
        ]);
    Route::get('/sair', [
        'as' => 'login.sair', 
        'uses' => 'App\Http\Controllers\LoginController@sair'
    ]);
    Route::get('/novoCadastro', [
        'as' => 'login.novoCadastro', 
        'uses' => 'App\Http\Controllers\LoginController@registrar'
    ]);
    Route::post('/salvarCadastro', [
        'as' => 'login.salvarCadastro', 
        'uses' => 'App\Http\Controllers\LoginController@salvar'
    ]);
    Route::get('/esqueciSenha', [
        'as' => 'login.esqueciSenha', 
        'uses' => 'App\Http\Controllers\LoginController@esqueciSenha'
    ]);
    Route::post('/enviarLink', [
        'as' => 'password.email', 
        'uses' => 'App\Http\Controllers\LoginController@enviarLinkRecuperacao'
    ]);
    Route::get('/recuperarSenha/{token}', [
        'as' => 'password.reset', 
        'uses' => 'App\Http\Controllers\LoginController@mostrarTelaRecuperarSenha'
    ]);
    Route::post('/atualizarSenha', [
        'as' => 'password.update', 
        'uses' => 'App\Http\Controllers\LoginController@atualizarSenha'
    ]);
});

Route::group(['prefix' => 'produtos'], function () {
    Route::get('/vitrine', [
        'as' => 'produtos.vitrine',
        'uses' => 'App\Http\Controllers\VitrineController@vitrine'
    ]);

    Route::get('/buscar', [
        'as' => 'produtos.buscar',
        'uses' => 'App\Http\Controllers\VitrineController@buscar'
    ]);

    Route::group(['middleware' => ['auth']], function () {
        Route::get('/detalheProduto/{id}', [
            'as' => 'produtos.detalheProduto',
            'uses' => 'App\Http\Controllers\VitrineController@detalheProduto'
        ]);

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
        'as'   => 'carrinho',
        'uses' => 'App\Http\Controllers\CarrinhoController@carrinho'
    ]);

    Route::post('/adicionar/{id}', [
        'as'   => 'carrinho.adicionar',
        'uses' => 'App\Http\Controllers\CarrinhoController@adicionar'
    ]);

    Route::post('/finalizar/{id_compra}', [
        'as'   => 'carrinho.finalizar',
        'uses' => 'App\Http\Controllers\CarrinhoController@finalizar'
    ]);

    Route::get('/conclusaoReserva', [
        'as'   => 'carrinho.conclusaoReserva',
        'uses' => 'App\Http\Controllers\CarrinhoController@conclusaoReserva'
    ]);

    Route::get('/remover/{id_produto}', [
        'as'   => 'carrinho.remover',
        'uses' => 'App\Http\Controllers\CarrinhoController@remover'
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
    Route::post('/atualizarDados', [
        'as' => 'perfil.atualizarDados', 
        'uses' => 'App\Http\Controllers\PerfilController@atualizarDados'
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
        'as' => 'perfil.minhasDoacoes.cancelar', 
        'uses' => 'App\Http\Controllers\PerfilController@cancelarDoacao'
    ]);
    Route::get('/minhasReservas/cancelar/{id}', [
        'as' => 'perfil.minhasReservas.cancelar', 
        'uses' => 'App\Http\Controllers\PerfilController@cancelarReserva'
    ]);
    Route::get('/minhasReservas/detalhes/{id}', [
        'as'   => 'perfil.minhasReservas.detalhes',
        'uses' => 'App\Http\Controllers\PerfilController@detalhesReserva'
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