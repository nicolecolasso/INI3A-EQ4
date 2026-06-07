<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doacao extends Model
{
    protected $table = 'doacao';

    protected $primaryKey = 'id_doacao';

    protected $fillable = [
        'nome',
        'categoria',
        'descricao',
        'caminho_img',
        'localizacao',
        'status',
        'data_doacao',
        'fk_doacao_id_usuario', 
    ];

    protected $casts = [
        'data_doacao' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'fk_doacao_id_usuario', 'id');
    }

    public function produtoGerado()
    {
        return $this->hasOne(Produto::class, 'fk_produto_id_doacao', 'id_doacao');
    }
}