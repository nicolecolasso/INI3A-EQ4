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
        'fk_compra_id_usuario'
    ];

    protected $casts = [
        'data_compra' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'fk_compra_id_usuario', 'id');
    }

    public function produto()
    {
        return $this->hasManyThrough(
            Produto::class,          // O modelo final que queremos acessar
            ProdutoReserva::class,   // O modelo intermediário
            'fk_id_compra',          // Chave estrangeira no modelo intermediário apontando para Compra
            'id_produto',            // Chave primária/estrangeira no modelo final (Produto)
            'id_compra',             // Chave primária no modelo Compra
            'fk_id_produto'          // Chave estrangeira no modelo intermediário apontando para Produto
        );
    }

    public function itens()
    {
        // Uma compra possui muitos registros na tabela intermediária
        return $this->hasMany(ProdutoReserva::class, 'fk_id_compra', 'id_compra');
    }

}
