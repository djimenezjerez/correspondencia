<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProceduresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procedures', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Código Hoja de Ruta');
            $table->string('origin')->comment('Procedencia');
            $table->string('detail')->comment('Detalle');
            $table->boolean('archived')->comment('Procedencia')->default(false);
            $table->unsignedBigInteger('area_id')->comment('Sección');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('pending')->default(true)->comment('Traḿite pendiente en la bandeja');
            $table->unsignedBigInteger('procedure_type_id')->comment('Tipo de Trámite');
            $table->foreign('procedure_type_id')->references('id')->on('procedure_types')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('procedures');
    }
}
