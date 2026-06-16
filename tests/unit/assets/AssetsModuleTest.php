<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Modules\Assets\Models\AssetCategory;
use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Models\AssetAssignment;
use App\User;

class AssetsModuleTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test creating a category and asset with auto code generation.
     */
    public function test_category_and_asset_creation()
    {
        $category = AssetCategory::create([
            'name' => 'Test Laptop',
            'description' => 'Test Laptops Category',
            'status' => 'Active'
        ]);

        $this->assertDatabaseHas('asset_categories', ['name' => 'Test Laptop']);

        $asset = Asset::create([
            'asset_name' => 'Test Macbook Air',
            'category_id' => $category->id,
            'brand' => 'Apple',
            'model' => 'Air',
            'serial_number' => 'MBA12345',
            'current_status' => 'Available',
            'condition' => 'Excellent'
        ]);

        $this->assertDatabaseHas('assets', ['asset_name' => 'Test Macbook Air']);
        $this->assertNotEmpty($asset->asset_code);
        $this->assertRegExp('/^AST-\d{4}$/', $asset->asset_code);
        
        $qrUrl = $asset->qr_code_url;
        $this->assertContains(route('assets.list.show', $asset->id), urldecode($qrUrl));
    }

    /**
     * Test checking out and returning assets.
     */
    public function test_asset_assignment_lifecycle()
    {
        $employee = User::create([
            'first_name' => 'Asset',
            'last_name' => 'Tester',
            'email' => 'assettester@example.com',
            'password' => bcrypt('password'),
            'role' => User::USER_ROLE_EMPLOYEE
        ]);

        $category = AssetCategory::create([
            'name' => 'Test Phones',
            'status' => 'Active'
        ]);

        $asset = Asset::create([
            'asset_name' => 'Test iPhone',
            'category_id' => $category->id,
            'current_status' => 'Available',
            'condition' => 'Good'
        ]);

        // Mock Checkout Assignment
        $assignment = AssetAssignment::create([
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'issue_date' => date('Y-m-d'),
            'assigned_by' => $employee->id,
            'status' => 'Active'
        ]);

        $asset->update(['current_status' => 'Assigned']);

        $this->assertDatabaseHas('asset_assignments', [
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'status' => 'Active'
        ]);

        $this->assertEquals('Assigned', $asset->fresh()->current_status);

        // Mock Return Check-in
        $assignment->update([
            'status' => 'Returned',
            'actual_return_date' => date('Y-m-d')
        ]);
        $asset->update(['current_status' => 'Available']);
        $this->assertEquals('Available', $asset->fresh()->current_status);
        $this->assertDatabaseHas('asset_assignments', [
            'id' => $assignment->id,
            'status' => 'Returned'
        ]);
    }

    public function test_non_soft_deletes_query_resolution()
    {
        $repo = app(\App\Modules\Assets\Repositories\Interfaces\AssetAssignmentRepositoryInterface::class);
        $collection = $repo->getCollection([]);
        $this->assertNotNull($collection);
    }
}
