<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnviosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('envios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('servico_id')->unsigned()->nullable();
            $table->index('servico_id');
            $table->integer('order_id')->unsigned()->nullable();
            $table->index('order_id');
            $table->string('etiqueta_id',255)->nullable();
            $table->string('status',100)->default('GERADO');
            $table->string('codigo_rastreio',100)->nullable();
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
        Schema::dropIfExists('envios');
    }
}
