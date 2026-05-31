<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compra';

    protected $primaryKey = 'id_compra';

    protected $fillable = [
        'status',
        'sessao',
        'data_compra',
        'fk_doacao_id_usuario',
    ];

    protected $casts = [
        'data_compra' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'fk_doacao_id_usuario', 'id');
    }
}
