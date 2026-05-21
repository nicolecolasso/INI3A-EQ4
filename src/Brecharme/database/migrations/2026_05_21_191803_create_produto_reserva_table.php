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
        Schema::create('produto_reserva', function (Blueprint $table) {
            $table->increments('id_produto_reserva');
            $table->foreignId('fk_doacao_id_produto')->references('id_produto')->on('produto')->onDelete('cascade');
            $table->foreignId('fk_doacao_id_compra')->references('id_compra')->on('compra')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_reserva');
    }
};
