<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatasToServico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('servicos', function (Blueprint $table) {
            $table->dateTimeTz('chegada_data')->nullable();
            $table->dateTimeTz('orcamento_data')->nullable();
            $table->dateTimeTz('autorizacao_data')->nullable();
            $table->dateTimeTz('manutencao_data')->nullable();
            $table->dateTimeTz('entrega_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servicos', function (Blueprint $table) {
            $table->dropColumn('chegada_data');
            $table->dropColumn('orcamento_data');
            $table->dropColumn('autorizacao_data');
            $table->dropColumn('manutencao_data');
            $table->dropColumn('entrega_data');
        });
    }
}
