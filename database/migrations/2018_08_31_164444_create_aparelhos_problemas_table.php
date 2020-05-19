<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAparelhosProblemasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aparelhos_problemas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('aparelho_id')->unsigned();
            $table->index('aparelho_id');
            $table->integer('problema_id')->unsigned();
            $table->index('problema_id');
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
        Schema::dropIfExists('aparelhos_problemas');
    }
}
