<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',          
        'email',         
        'password',      
        'telefone',      
        'admin',         
        'excluido',     
        'data_exclusao' 
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime', 
        'password' => 'hashed', 
        'excluido' => 'boolean',           
        'data_exclusao' => 'datetime',    
    ];

    public function doacoes()
    {
        return $this->hasMany(Doacao::class, 'fk_doacao_id_usuario', 'id');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'fk_compra_id_usuario', 'id');
    }
}