<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSomeNullableToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('cep',100)->nullable()->change();
            $table->string('rua',100)->nullable()->change();
            $table->string('numero',100)->nullable()->change();
            $table->string('bairro',100)->nullable()->change();
            $table->string('cidade',100)->nullable()->change();
            $table->string('estado',100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('cep',100)->nullable(false)->change();
            $table->string('rua',100)->nullable(false)->change();
            $table->string('numero',100)->nullable(false)->change();
            $table->string('bairro',100)->nullable(false)->change();
            $table->string('cidade',100)->nullable(false)->change();
            $table->string('estado',100)->nullable(false)->change();
        });
    }
}
