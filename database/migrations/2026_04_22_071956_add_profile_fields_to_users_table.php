<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('last_name');
            $table->string('phone_number')->nullable()->after('google_token');
            $table->string('student_id')->nullable()->after('phone_number');
            $table->string('department_name')->nullable()->after('student_id');
            $table->string('course')->nullable()->after('department_name');
            $table->string('year_level')->nullable()->after('course');
            $table->text('bio')->nullable()->after('year_level');
            $table->timestamp('profile_completed_at')->nullable()->after('bio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'phone_number',
                'student_id',
                'department_name',
                'course',
                'year_level',
                'bio',
                'profile_completed_at',
            ]);
        });
    }
}
