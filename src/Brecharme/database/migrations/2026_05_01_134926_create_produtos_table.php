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
        Schema::create('produto', function (Blueprint $table) {
            $table->increments('id_produto'); 
            $table->string('nome');
            $table->text('descricao');
            $table->decimal('valor', 8, 2);
            $table->string('caminho_img');
            $table->enum('categoria', ['Roupas', 'Calçados', 'Acessórios', 'Eletrônicos', 'Móveis', 'Brinquedos', 'Outros'])->default('Outros');
            $table->timestamp('data_exclusao')->nullable();
            $table-> boolean('excluido')->default(false);
            $table->enum('status', ['Disponível', 'Carrinho', 'Vendido', 'Reservado'])->default('Disponível');

            $table->unsignedInteger('fk_produto_id_doacao')->nullable();
            $table->foreign('fk_produto_id_doacao')->references('id_doacao')->on('doacao')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto');
    }
};
