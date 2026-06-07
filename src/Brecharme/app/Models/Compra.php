<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compra';

    protected $primaryKey = 'id_compra';

    protected $fillable = [
        'status',
        'data_compra',
        'fk_compra_id_usuario'
    ];

    protected $casts = [
        'data_compra' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'fk_compra_id_usuario', 'id');
    }

    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'produto_reserva', 'fk_id_compra', 'fk_id_produto')
                    ->withPivot('id_produto_reserva', 'status')
                    ->withTimestamps();
    }

    public function itens()
    {
        return $this->hasMany(ProdutoReserva::class, 'fk_id_compra', 'id_compra');
    }
}