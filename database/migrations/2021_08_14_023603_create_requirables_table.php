<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequirablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requirables', function (Blueprint $table) {
            $table->morphs('requirable');
            $table->unsignedBigInteger('requirement_id')->comment('Requisito');
            $table->foreign('requirement_id')->references('id')->on('requirements')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('validated')->default(false)->comment('Requisito validado');
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
        Schema::dropIfExists('requirables');
    }
}
