<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAttendancesTable extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dateTime('check_in')->nullable()->change();
            
            $table->integer('attendance_rule_id')->unsigned()->nullable();
            $table->integer('company_id')->unsigned()->nullable();
            $table->string('check_in_selfie')->nullable();
            $table->string('check_out_selfie')->nullable();
            $table->decimal('check_in_latitude', 10, 8)->nullable();
            $table->decimal('check_in_longitude', 11, 8)->nullable();
            $table->decimal('check_out_latitude', 10, 8)->nullable();
            $table->decimal('check_out_longitude', 11, 8)->nullable();
            $table->string('check_out_ip')->nullable();
            $table->string('device_fingerprint')->nullable();
            $table->integer('work_duration_minutes')->nullable();
            
            $table->foreign('attendance_rule_id')
                ->references('id')
                ->on('attendance_rules')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dateTime('check_in')->nullable(false)->change();
            
            $table->dropForeign(['attendance_rule_id']);
            $table->dropColumn([
                'attendance_rule_id',
                'company_id',
                'check_in_selfie',
                'check_out_selfie',
                'check_in_latitude',
                'check_in_longitude',
                'check_out_latitude',
                'check_out_longitude',
                'check_out_ip',
                'device_fingerprint',
                'work_duration_minutes'
            ]);
        });
    }
}
