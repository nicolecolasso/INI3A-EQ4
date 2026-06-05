<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/*
    Os atributos abaixo (#[]) ajudam as ferramentas do VS Code a entender quais campos existem no banco de dados.
 */
#[Fillable(['name', 'email', 'password', 'telefone', 'admin', 'excluido', 'data_exclusao'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use Notifiable;

    /*
    Sempre que  criar uma coluna nova na tabela 'users' (via migration) 
    e precisar salvar esse dado direto de um formulário ou do Controller, 
    deve-se colocar o nome da coluna aqui dentro desse array. 
    Se esquecer de colocar, o Laravel bloqueia o salvamento por segurança
    */
    protected $fillable = [
        'name',          
        'email',         
        'password',      
        'telefone',      
        'admin',         
        'excluido',     
        'data_exclusao' 
    ];

    /*
    CAMPOS OCULTOS 
    Tudo o que estiver aqui dentro NUNCA será exibido se você tentar converter 
    o usuário em texto ou JSON. Protege a senha criptografada e o token do "Manter-me conectado" 
    de vazarem para o front-end por acidente.
    */
    protected $hidden = [
        'password', 
        'remember_token'
    ];

    /*Conversões automáticas
    O Laravel transforma os dados do banco para o tipo certo no PHP automaticamente.
    */
    protected $casts = [
        'email_verified_at' => 'datetime', // Transforma string do banco em um objeto de Data/Hora do PHP
        
        // 'password' => 'hashed' diz ao Laravel que SEMPRE que você salvar ou alterar 
        // a senha, ele deve aplicar a criptografia Bcrypt automaticamente. Não precisa usar Hash::make() no cadastro
        'password' => 'hashed', 
        
        'excluido' => 'boolean',           
        'data_exclusao' => 'datetime',    
    ];

    /*
    RELACIONAMENTO: Usuário tem muitas doações (1 para Muitos)
    Permite buscar as doações do usuário logado direto no Controller usando: 
    Auth::user()->doacoes
    Parâmetros do hasMany:
    1º: O modelo com quem se conecta (Doacao::class)
    2º: A coluna na tabela de doações que guarda o ID do usuário (fk_doacao_id_usuario)
    3º: A chave primária deste modelo atual (id)
    */
    public function doacoes()
    {
        return $this->hasMany(Doacao::class, 'fk_doacao_id_usuario', 'id');
    }

    /*
    Usuário tem muitas compras/reservas (1 para Muitos)
    Permite buscar o histórico ou fazer a contagem de reservas usando:
    Auth::user()->compras
    Parâmetros do hasMany:
    1º: O modelo com quem se conecta (Compra::class)
    2º: A coluna na tabela de compras que guarda o ID do usuário (fk_compra_id_usuario)
    3º: A chave primária deste modelo atual (id)
    */
    public function compras()
    {
        return $this->hasMany(Compra::class, 'fk_compra_id_usuario', 'id');
    }
}