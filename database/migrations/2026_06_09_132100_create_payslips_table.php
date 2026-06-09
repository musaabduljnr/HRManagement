<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayslipsTable extends Migration
{
    public function up()
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payroll_record_id')->unsigned();
            $table->foreign('payroll_record_id')->references('id')->on('payroll_records')->onDelete('cascade');
            $table->string('payslip_number');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payslips');
    }
}
