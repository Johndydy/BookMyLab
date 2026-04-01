<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_logs', function (Blueprint $table) {
            $table->increments('equipmentlog_id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('equipment_id');
            $table->integer('quantity_borrowed');
            $table->dateTime('borrowed_at');
            $table->dateTime('returned_at')->nullable();
            $table->enum('condition_after', ['good', 'damaged'])->nullable();
            $table->timestamps();
            
            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
            $table->foreign('equipment_id')->references('equipment_id')->on('equipment')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment_logs');
    }
}
