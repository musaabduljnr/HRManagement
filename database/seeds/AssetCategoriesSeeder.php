<?php

use Illuminate\Database\Seeder;

class AssetCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Laptops', 'description' => 'Company-issued laptops (MacBooks, ThinkPads, etc.)', 'status' => 'Active'],
            ['name' => 'Desktop Computers', 'description' => 'Workstation towers, iMacs, and monitors', 'status' => 'Active'],
            ['name' => 'Phones', 'description' => 'Mobile phones issued for work communications', 'status' => 'Active'],
            ['name' => 'Tablets', 'description' => 'iPads and Android tablets used in testing or operations', 'status' => 'Active'],
            ['name' => 'SIM Cards', 'description' => 'Work SIM cards with mobile data/calls enabled', 'status' => 'Active'],
            ['name' => 'ID Cards', 'description' => 'Employee security ID badges and keycards', 'status' => 'Active'],
            ['name' => 'Vehicles', 'description' => 'Company cars, delivery vans, and fleet vehicles', 'status' => 'Active'],
            ['name' => 'Office Equipment', 'description' => 'Printers, chairs, standing desks, etc.', 'status' => 'Active'],
        ];

        foreach ($categories as $category) {
            DB::table('asset_categories')->insert($category + [
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
