<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetModuleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('Active'); // Active, Inactive
            $table->timestamps();
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('asset_code')->unique()->index();
            $table->string('asset_name');
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('asset_categories')->onDelete('cascade');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 10, 2)->nullable();
            $table->string('supplier')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->string('current_status')->default('Available'); // Available, Assigned, Under Maintenance, Damaged, Lost, Retired
            $table->string('condition')->default('Excellent'); // Excellent, Good, Fair, Poor, Damaged
            $table->text('notes')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asset_id')->unsigned();
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->integer('employee_id')->unsigned();
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
            $table->date('issue_date');
            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->integer('assigned_by')->unsigned();
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');
            $table->string('received_by')->nullable();
            $table->text('assignment_notes')->nullable();
            $table->string('status')->default('Active'); // Active, Returned, Replaced, Lost
            $table->timestamps();
        });

        Schema::create('asset_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asset_id')->unsigned();
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->string('action_type'); // Created, Assigned, Returned, Replaced, Condition Updated, Maintenance, Lost, Retired
            $table->integer('performed_by')->unsigned();
            $table->foreign('performed_by')->references('id')->on('users')->onDelete('cascade');
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asset_id')->unsigned();
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->string('maintenance_type'); // Repairs, Servicing, Software upgrades, Hardware replacement
            $table->text('description');
            $table->decimal('cost', 10, 2)->default(0.00);
            $table->string('service_provider')->nullable();
            $table->date('maintenance_date');
            $table->date('next_maintenance_date')->nullable();
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
        Schema::dropIfExists('asset_maintenances');
        Schema::dropIfExists('asset_histories');
        Schema::dropIfExists('asset_assignments');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_categories');
    }
}
