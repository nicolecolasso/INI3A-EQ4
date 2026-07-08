<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            'Roupas Masculinas',
            'Roupas Femininas',
            'Calçados',
            'Acessórios',
            'Bolsas',
        ];

        foreach ($categorias as $nome) {
            Categoria::firstOrCreate([
                'nome' => $nome
            ]);
        }
    }
}