<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPesoEtcToProdutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->decimal('comprimento', 16, 4)->default(0);
            $table->decimal('largura', 16, 4)->default(0);
            $table->decimal('altura', 16,4)->default(0);
            $table->decimal('peso', 16, 4)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn('comprimento');
            $table->dropColumn('largura');
            $table->dropColumn('altura');
            $table->dropColumn('peso');
        });
    }
}
