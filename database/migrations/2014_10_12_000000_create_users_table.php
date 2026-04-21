<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('user_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('school_email')->unique();
            $table->string('school_id_number')->unique();
            $table->string('password');
            // Role is managed via user_roles + roles tables. No role enum here.
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}