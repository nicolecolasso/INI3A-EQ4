<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doacao', function (Blueprint $table) {
            $table->increments('id_doacao');
            $table->enum('categoria', ['Roupas', 'Calçados', 'Acessórios', 'Eletrônicos', 'Móveis', 'Brinquedos', 'Outros'])->default('Outros');
            $table->text('descricao');
            $table->string('caminho_img');
            $table->string('localizacao');
            $table->enum('status', ['Analise', 'Aprovada', 'Rejeitada', 'Retirada'])->default('Analise');
            $table->timestamp('data_doacao')->useCurrent();

            $table->foreignId('fk_doacao_id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doacaos');
    }
};
