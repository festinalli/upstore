<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCodigoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('codigos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo',20)->nullable();
            $table->integer('user_id')->unsigned()->default(0);
            $table->index('user_id');
            $table->string('status',100)->default('ATIVO');
            $table->string('valido_ate',100)->nullable();
            $table->integer('porcentagem')->default(0);
            $table->integer('valor')->default(0);
            $table->integer('servico_id')->unsigned()->nullable();
            $table->index('servico_id');
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
        Schema::dropIfExists('codigos');
    }
}
