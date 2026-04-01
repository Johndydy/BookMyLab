<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->bigIncrements('equipment_id');
            $table->unsignedBigInteger('laboratory_id');
            $table->string('name');
            $table->integer('quantity');
            $table->enum('condition', ['good', 'damaged', 'under repair'])->default('good');
            $table->timestamps();

            $table->foreign('laboratory_id')->references('laboratory_id')->on('laboratories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment');
    }
}
