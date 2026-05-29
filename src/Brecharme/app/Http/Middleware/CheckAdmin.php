<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdmin
{
    /**
     * Trata a requisição antes de chegar nas rotas
     */
    public function handle(Request $request, Closure $next)
    {
        $usuarioLogado = auth()->user();

        if (!$usuarioLogado || $usuarioLogado->admin == false) {

            return redirect()->route('institucional.index')->with('erro', 'Você não é admin.');
        }

       
        return $next($request);
    }
}