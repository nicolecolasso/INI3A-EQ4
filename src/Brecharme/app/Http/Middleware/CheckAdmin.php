<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $usuarioLogado = Auth::user();

        if (!$usuarioLogado || $usuarioLogado->admin == false) {

            return redirect()->route('institucional.index')->with('erro', 'Você não é admin ou não está logado.');
        }
       
        return $next($request);
    }
}