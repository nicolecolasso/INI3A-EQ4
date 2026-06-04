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
        Schema::create('comunicado', function (Blueprint $table) {
            $table->increments('id_comunicado');
            $table->string('assunto');
            $table->text('mensagem');
            $table->string('status')->default('pendente');
            $table->timestamp('data_envio')->useCurrent();

            $table->foreignId('fk_comunicado_id_usuario')->references('id')->on('users')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comunicado');
    }
};
