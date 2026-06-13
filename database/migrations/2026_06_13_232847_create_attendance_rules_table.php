<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable();
            $table->string('rule_name');
            $table->string('shift_name');
            $table->integer('branch_id')->unsigned()->nullable();
            $table->integer('department_id')->unsigned()->nullable();
            $table->enum('applies_to', ['all_employees', 'branch', 'department', 'selected_employees'])->default('all_employees');
            $table->text('employee_ids')->nullable(); // stored as JSON string
            $table->text('working_days'); // stored as JSON string
            $table->time('check_in_start_time');
            $table->integer('grace_period_minutes')->default(0);
            $table->time('check_in_cutoff_time');
            $table->boolean('check_out_enabled')->default(true);
            $table->time('check_out_start_time')->nullable();
            $table->time('check_out_cutoff_time')->nullable();
            $table->integer('minimum_work_duration_minutes')->default(0);
            $table->decimal('office_latitude', 10, 8)->nullable();
            $table->decimal('office_longitude', 11, 8)->nullable();
            $table->integer('allowed_radius_meters')->nullable();
            $table->boolean('selfie_required')->default(false);
            $table->boolean('checkout_selfie_required')->default(false);
            $table->boolean('device_lock_required')->default(false);
            $table->boolean('auto_mark_absent')->default(true);
            $table->boolean('auto_mark_missed_checkout')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('created_by')->unsigned()->nullable();
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
        Schema::dropIfExists('attendance_rules');
    }
}
