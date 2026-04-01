<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_equipment', function (Blueprint $table) {
            $table->bigIncrements('bookingequipment_id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('equipment_id');
            $table->integer('quantity_requested');
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
        Schema::dropIfExists('booking_equipment');
    }
}
