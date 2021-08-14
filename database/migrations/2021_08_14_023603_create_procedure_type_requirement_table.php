<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcedureTypeRequirementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procedure_type_requirement', function (Blueprint $table) {
            $table->unsignedBigInteger('procedure_type_id')->comment('Tipo de Trámite');
            $table->foreign('procedure_type_id')->references('id')->on('procedure_types')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('requirement_id')->comment('Requisito');
            $table->foreign('requirement_id')->references('id')->on('requirements')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('procedure_type_requirement');
    }
}
