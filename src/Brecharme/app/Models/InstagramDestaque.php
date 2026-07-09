<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstagramDestaque extends Model
{
    protected $table = 'instagram_destaques';
    protected $primaryKey = 'id_destaque';
    protected $fillable = ['link_post'];
}
