<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLojasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lojas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('titulo',100);
            $table->string('cnpj',100)->unique();
            $table->string('cep',100);
            $table->string('endereco',100);
            $table->string('cidade',100);
            $table->string('bairro',100);
            $table->string('numero',100);
            $table->string('estado',100);
            $table->string('status',100);
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
        Schema::dropIfExists('lojas');
    }
}
