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
        Schema::create('compra', function (Blueprint $table) {
            $table->increments('id_compra');
            $table->enum('status', ['Carrinho', 'Reservado', 'Concluída', 'Cancelada'])->default('Carrinho');
            $table->string('sessao');
            $table->timestamp('data_compra')->useCurrent();

            $table->foreignId('fk_compra_id_produto')->references('id_produto')->on('produto')->onDelete('cascade');
            $table->foreignId('fk_compra_id_usuario')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compra');
    }
};
