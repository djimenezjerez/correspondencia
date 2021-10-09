<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDocumentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['document_type_id']);
            $table->dropColumn('document_type_id');
        });
        Schema::dropIfExists('document_types');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nombre');
            $table->string('code')->unique()->comment('Código');
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('document_type_id')->comment('Tipo de Documento de Identidad');
            $table->foreign('document_type_id')->references('id')->on('document_types')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }
}
