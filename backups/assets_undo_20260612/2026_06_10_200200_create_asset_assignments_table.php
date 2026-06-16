<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetAssignmentsTable extends Migration
{
    public function up()
    {
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('asset_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('assigned_by');
            $table->date('assigned_date');
            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->enum('status', ['active', 'returned'])->default('active');
            $table->enum('condition_at_assignment', ['excellent', 'good', 'fair', 'poor'])->default('good');
            $table->enum('condition_at_return', ['excellent', 'good', 'fair', 'poor'])->nullable();
            $table->text('notes')->nullable();
            $table->text('return_notes')->nullable();
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('assigned_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('asset_assignments');
    }
}
