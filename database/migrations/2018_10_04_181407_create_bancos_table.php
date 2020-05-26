<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBancosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bancos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('banco',100);
            $table->string('agencia',20);
            $table->string('conta',20);
            $table->string('tipo_conta',100);
            $table->string('titular',191);
            $table->string('documento_titular',20);
            $table->string('foto_comprovante',191)->nullable();
            $table->integer('servico_id')->unsigned();
            $table->index('servico_id');
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
        Schema::dropIfExists('bancos');
    }
}
