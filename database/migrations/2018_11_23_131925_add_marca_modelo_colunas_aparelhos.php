<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMarcaModeloColunasAparelhos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aparelhos', function (Blueprint $table) {
            $table->integer('marca_id')->unsinged()->default(0);
            $table->index('marca_id');
            
            $table->integer('modelo_id')->unsinged()->default(0);
            $table->index('modelo_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aparelhos', function (Blueprint $table) {
            //
        });
    }
}
