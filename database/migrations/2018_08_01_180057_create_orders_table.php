<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->default(0);
            $table->index('user_id');
            $table->integer('endereco_id')->unsigned()->default(0);
            $table->index('endereco_id');
            $table->integer('cartao_id')->unsigned()->default(0);
            $table->index('cartao_id');
            $table->integer('codigo_id')->unsigned()->default(0);
            $table->index('codigo_id');
            
            $table->string('gateway_order_id',100)->nullable();
            $table->string('gateway_payment_id',100)->nullable();
            $table->string('forma_pagamento',100)->nullable();
            $table->string('status',100)->default('CARRINHO');//CARRINHO | ANALISE | PAGO | N_PAGO
            $table->integer('frete_valor')->default(0);
            $table->integer('frete_prazo')->default(0);
            $table->integer('valor_total')->default(0);
            $table->integer('desconto_valor')->default(0);
            $table->string('link_pagamento',200)->nullable();
            $table->integer('parcelamento')->default(1);

            $table->string('hash_cartao',500)->nullalbe();
            $table->boolean('troca')->default(false);

            $table->string('token',200);
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
        Schema::dropIfExists('orders');
    }
}
