<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cartoes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->index('user_id');
            $table->string('hash',500);
            $table->string('ultimos4',100)->nullable();
            $table->string('bandeira',100)->nullable();
            $table->string('ano',100)->nullable();
            $table->string('mes',100)->nullable();
            $table->string('cvc',100)->nullable();
            $table->string('status',100)->nullable();

            $table->string('holder_nome',100);
            $table->string('holder_data_nascimento',100);
            $table->string('holder_cpf',100);
            $table->string('holder_telefone',100);
            $table->string('moip_card_id',100)->nullable();
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
        Schema::dropIfExists('cartoes');
    }
}
