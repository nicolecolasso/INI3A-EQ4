<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $table = 'categorias';
    
    protected $primaryKey = 'id_categoria';

    protected $fillable = ['nome'];

    /* Uma categoria tem muitos produtos vinculados a ela */
    public function produtos(): HasMany
    {
        return $this->hasMany(Produto::class, 'fk_id_categoria', 'id_categoria');
    }

    public function doacoes(): HasMany
    {
        return $this->hasMany(Doacao::class, 'fk_id_categoria', 'id_categoria');
    }
}