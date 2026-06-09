<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCandidatesTable extends Migration
{
    public function up()
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('job_opening_id')->unsigned()->nullable();
            $table->foreign('job_opening_id')->references('id')->on('job_openings')->onDelete('set null');
            $table->string('status')->default('Applied'); // Applied, Shortlisted, Interviewing, Offered, Hired, Rejected
            $table->string('resume_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('candidates');
    }
}
