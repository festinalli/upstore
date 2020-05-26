<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->default(0);
            $table->index('user_id');
            $table->integer('produto_id')->unsigned();
            $table->index('produto_id');
            $table->integer('loja_id')->unsigned();
            $table->index('loja_id');
            $table->integer('order_id')->unsigned();
            $table->index('order_id');

            $table->string('nome',100);
            $table->text('descricao');
            $table->integer('valor_unitario');
            $table->integer('quantidade');

            $table->string('status',100);//CARRINHO | ANALISE | PAGO | N_PAGO
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
        Schema::dropIfExists('vendas');
    }
}
