<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nombres');
            $table->string('last_name')->comment('Apellidos');
            $table->string('username')->unique()->comment('Nombre de usuario');
            $table->string('identity_card')->unique()->comment('Documento de Identidad');
            $table->string('password')->comment('Contraseña');
            $table->string('email')->comment('Correo Electrónico');
            $table->string('address')->comment('Dirección');
            $table->integer('phone')->comment('Teléfono');
            $table->unsignedBigInteger('document_type_id')->comment('Tipo de Documento de Identidad');
            $table->foreign('document_type_id')->references('id')->on('document_types')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('area_id')->comment('Sección');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade')->onUpdate('cascade');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
