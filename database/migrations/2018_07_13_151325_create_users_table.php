<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome',100);
            $table->string('sobrenome',100);
            $table->string('email',100);
            $table->string('customer_id',100)->nullable();//moip customer id
            $table->string('telefone',100)->nullable();
            $table->string('documento',15)->nullable();// CPF OR CNPJ
            $table->string('sexo',1)->nullable();//M | F
            $table->string('status',100);//ACTIVE | INACTIVE
            $table->string('tipo',100);//ADMIN | CLIENT | EMPLOYEE
            $table->string('data_nascimento',100)->nullable();
            $table->string('rua',100)->nullable();
            $table->string('numero',100)->nullable();
            $table->string('bairro',100)->nullable();
            $table->string('cidade',100)->nullable();
            $table->string('estado',100)->nullable();
            $table->string('cep',100)->nullable();
            $table->string('password',100);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
