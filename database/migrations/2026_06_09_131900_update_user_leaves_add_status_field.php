<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateUserLeavesAddStatusField extends Migration
{
    public function up()
    {
        Schema::table('user_leaves', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('approved');
        });

        // Migrate existing approved boolean data to status string
        DB::table('user_leaves')->where('approved', 1)->update(['status' => 'approved']);
        DB::table('user_leaves')->where('approved', 0)->update(['status' => 'rejected']);
    }

    public function down()
    {
        Schema::table('user_leaves', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
