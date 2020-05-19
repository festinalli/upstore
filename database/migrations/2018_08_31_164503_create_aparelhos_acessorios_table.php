<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAparelhosAcessoriosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aparelhos_acessorios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('aparelho_id')->unsigned();
            $table->index('aparelho_id');
            $table->integer('acessorio_id')->unsigned();
            $table->index('acessorio_id');
            $table->boolean('valido')->default(false);
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
        Schema::dropIfExists('aparelhos_acessorios');
    }
}
