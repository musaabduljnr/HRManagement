<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Modules\Assets\Models\AssetCategory;
use App\Modules\Assets\Models\Asset;

class AssetsModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_an_asset_category()
    {
        $cat = AssetCategory::create(['name' => 'Laptop', 'slug' => 'laptop']);
        $this->assertDatabaseHas('asset_categories', ['name' => 'Laptop']);
        $this->assertEquals('laptop', $cat->slug);
    }

    /** @test */
    public function it_creates_an_asset_and_generates_tag()
    {
        $cat = AssetCategory::create(['name' => 'Phone', 'slug' => 'phone']);
        $asset = Asset::create([
            'asset_tag' => 'AST-0001',
            'name' => 'iPhone',
            'category_id' => $cat->id
        ]);
        $this->assertDatabaseHas('assets', ['name' => 'iPhone']);
        $this->assertEquals('AST-0001', $asset->asset_tag);
    }
}
