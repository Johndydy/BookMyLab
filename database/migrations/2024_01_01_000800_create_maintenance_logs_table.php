<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceLogsTable extends Migration
{
    public function up()
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->bigIncrements('log_id');
            $table->unsignedBigInteger('laboratory_id');
            $table->unsignedBigInteger('admin_id');
            $table->text('reason');
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->timestamps();

            $table->foreign('laboratory_id')->references('laboratory_id')->on('laboratories')->onDelete('cascade');
            $table->foreign('admin_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_logs');
    }
}