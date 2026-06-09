<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollRecordsTable extends Migration
{
    public function up()
    {
        Schema::create('payroll_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('payroll_month'); // e.g. "2026-06"
            $table->decimal('base_salary', 10, 2)->default(0.00);
            $table->decimal('allowances', 10, 2)->default(0.00);
            $table->decimal('deductions', 10, 2)->default(0.00);
            $table->decimal('bonuses', 10, 2)->default(0.00);
            $table->decimal('net_salary', 10, 2)->default(0.00);
            $table->string('status')->default('draft'); // draft, paid
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_records');
    }
}
