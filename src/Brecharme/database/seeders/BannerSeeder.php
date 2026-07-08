<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{

    public function run(): void
    {
        $fotosDefault = [
            1 => 'img/brecharme1.png',
            2 => 'img/brecharme2.png',
            3 => 'img/brecharme3.png',
        ];

        foreach ($fotosDefault as $ordem => $caminho) {
            Banner::firstOrCreate(
                ['ordem' => $ordem],
                ['caminho_img' => $caminho]
            );
        }
    }
}
