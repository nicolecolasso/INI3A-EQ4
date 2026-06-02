<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Administrador Brecharme',
            'email'    => 'admin@brecharme.com',
            'password' => Hash::make('SistemaCaritas123'),
            'admin'    => true,
            'telefone' => '14991083780' 
        ]);
    }
}
