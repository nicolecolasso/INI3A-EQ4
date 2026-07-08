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
            $table->string('nome');
            $table->text('descricao');
            $table->string('caminho_img');
            $table->string('localizacao')->nullable();
            $table->enum('status', ['Em Análise', 'Aprovada', 'Integrada ao Estoque', 'Recusada', 'Cancelada'])->default('Em Análise');
            $table->timestamp('data_doacao')->useCurrent();

            $table->foreignId('fk_doacao_id_usuario')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('fk_id_categoria')->constrained('categorias', 'id_categoria');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doacao');
    }
};
