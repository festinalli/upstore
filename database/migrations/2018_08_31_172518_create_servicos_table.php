<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('aparelho_id')->unsigned();
            $table->index('aparelho_id');
            $table->integer('loja_id')->unsigned();
            $table->index('loja_id');
            $table->integer('user_id')->unsigned();
            $table->index('user_id');
            $table->integer('order_id')->unsigned()->default(0);
            $table->index('order_id');
            $table->string('status',100)->default('CRIADO');
            $table->string('metodo',100);
            $table->string('tipo',1);
            $table->integer('valor')->default(0);
            $table->boolean('deposito_cupom')->default(0);//0 - deposito, 1 - cupom
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
        Schema::dropIfExists('servicos');
    }
}
