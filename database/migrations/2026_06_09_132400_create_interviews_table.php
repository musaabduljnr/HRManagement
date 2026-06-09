<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterviewsTable extends Migration
{
    public function up()
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->unsigned();
            $table->foreign('candidate_id')->references('id')->on('candidates')->onDelete('cascade');
            $table->dateTime('interview_date');
            $table->string('interviewer_name')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('Scheduled'); // Scheduled, Completed, Cancelled
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('interviews');
    }
}
