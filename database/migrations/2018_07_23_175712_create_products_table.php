<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome',100);
            $table->text('descricao');
            $table->integer('valor');
            $table->string('voltagem',100);
            $table->integer('quantidade');
            $table->string('status');
            $table->integer('destaque')->default(0);
            $table->integer('semi_novo')->default(0);
            $table->integer('marca_id')->unsigned()->default(0);
            $table->integer('modelo_id')->unsigned()->default(0);
            $table->integer('capacidade_id')->unsigned()->default(0);
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
        Schema::dropIfExists('products');
    }
}
