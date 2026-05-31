<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdutoReserva extends Model
{

    protected $table = 'produto_reserva';

    protected $primaryKey = 'id_produto_reserva';

    protected $fillable = [
        'fk_doacao_id_produto',
        'fk_doacao_id_compra',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'fk_doacao_id_produto', 'id_produto');
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'fk_doacao_id_compra', 'id_compra');
    }
}