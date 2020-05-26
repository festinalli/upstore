<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProdutoEstoqueLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produto_estoque_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('produto_id')->unsigned();
            $table->index('produto_id');
            $table->integer('carrinho_id')->unsigned();
            $table->index('carrinho_id');
            $table->integer('loja_id')->unsigned();
            $table->index('loja_id');
            $table->integer('quantidade');
            $table->string('tipo',100);//ADD,SUB
            $table->integer('quantidade_anterior');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produto_estoque_logs');
    }
}
