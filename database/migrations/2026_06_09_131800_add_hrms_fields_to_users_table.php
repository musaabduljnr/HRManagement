<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHrmsFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('department_id')->unsigned()->nullable()->after('role');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');

            $table->integer('job_title_id')->unsigned()->nullable()->after('department_id');
            $table->foreign('job_title_id')->references('id')->on('job_titles')->onDelete('set null');

            $table->string('employment_status')->nullable()->after('job_title_id'); // e.g. Full-Time, Part-Time, Contract, Intern

            $table->string('emergency_contact_name')->nullable()->after('employment_status');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys first if not sqlite, but in SQLite we just drop columns.
            // Since SQLite has limited support for dropping columns in some versions, 
            // standard syntax is fine as Laravel handles SQLite dropColumns fallback.
            $table->dropColumn([
                'department_id',
                'job_title_id',
                'employment_status',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relationship'
            ]);
        });
    }
}
