<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrPoliciesTable extends Migration
{
    public function up()
    {
        Schema::create('hr_policies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('category')->nullable(); // e.g. Conduct, Leave, Benefits
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hr_policies');
    }
}
