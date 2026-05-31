<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comunicado extends Model
{
    protected $table = 'comunicado';

    protected $primaryKey = 'id_comunicado';

    protected $fillable = [
        'assunto',
        'mensagem',
        'data_envio',
        'fk_comunicado_id_usuario',
    ];

    protected $casts = [
        'data_envio' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'fk_comunicado_id_usuario', 'id');
    }
}
