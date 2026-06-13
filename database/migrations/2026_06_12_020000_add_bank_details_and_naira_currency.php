<?php
// C:\Users\user\OneDrive\Desktop\HRMan\hrm\database\migrations\2026_06_12_020000_add_bank_details_and_naira_currency.php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddBankDetailsAndNairaCurrency extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('notes');
            $table->string('account_number')->nullable()->after('bank_name');
        });

        // Insert Naira currency if it doesn't exist
        $nairaExists = DB::table('currencies')->where('currency_code', 'NGN')->exists();
        if (!$nairaExists) {
            DB::table('currencies')->insert([
                'currency_code' => 'NGN',
                'currency_display' => '₦',
            ]);
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'account_number']);
        });

        DB::table('currencies')->where('currency_code', 'NGN')->delete();
    }
}
