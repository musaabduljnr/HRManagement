<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    protected $table = 'asset_categories';
    protected $guarded = ['id'];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'category_id');
    }
}
