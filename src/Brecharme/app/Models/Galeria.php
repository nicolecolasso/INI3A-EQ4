<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Galeria extends Model
{
    protected $table = 'galerias';
    protected $primaryKey = 'id_galeria';
    protected $fillable = ['caminho_img', 'titulo_evento'];
}
