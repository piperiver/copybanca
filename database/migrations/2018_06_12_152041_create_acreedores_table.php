<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAcreedoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acreedores', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('nombre');
            $table->string('tipo')->default('');
            $table->string('sector')->default('');
            $table->string('clasificacion')->default('');
            $table->string('sitio_web')->default('');
            $table->string('nit')->default('');
            $table->string('domicilio')->default('');
            $table->string('cuenta')->default('');
            $table->string('tipo_cuenta')->default('');
            $table->string('entidad_desembolso')->default('');
            $table->string('ciudad')->default('');
            $table->string('departamento')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acreedores');
    }
}
