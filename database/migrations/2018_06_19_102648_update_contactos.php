<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContactos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contactos', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('Nombre');
            $table->string('Entidad')->default('');
            $table->string('Cargo')->default('');
            $table->string('Telefono')->default('');
            $table->string('Correo')->default('');
            $table->string('Area')->default('');
            $table->unsignedInteger('acreedor_id');
            $table->foreign('acreedor_id')->references('id')->on('acreedores');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contactos');
    }
}
