<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProblemaDescricaoToAparelho extends Migration
{
    public function up()
    {
        Schema::table('aparelhos', function (Blueprint $table) {
            $table->text('problema_descricao')->nullable();
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
            $table->dropColumn('problema_descricao');
        });
    }
}
