<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $table = 'produto';

    protected $primaryKey = 'id_produto';

    protected $fillable = [
        'nome',
        'descricao',
        'valor',
        'caminho_img',
        'categoria',
        'data_exclusao',
        'excluido',
        'status',
        'fk_produto_id_doacao',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'excluido' => 'boolean',
        'data_exclusao' => 'datetime',
    ];

    public function doacao()
    {
        return $this->belongsTo(Doacao::class, 'fk_produto_id_doacao', 'id_doacao');
    }

    public function compras()
    {
        return $this->belongsToMany(Compra::class, 'produto_reserva', 'fk_id_produto', 'fk_id_compra')
                    ->withPivot('id_produto_reserva', 'status')
                    ->withTimestamps();
    }

    public function reservas()
    {
        return $this->hasMany(ProdutoReserva::class, 'fk_id_produto', 'id_produto');
    }
}