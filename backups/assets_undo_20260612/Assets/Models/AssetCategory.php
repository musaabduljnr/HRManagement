<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetCategory extends Model
{
    use SoftDeletes;

    protected $table = 'asset_categories';

    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'category_id');
    }

    public function getActiveAssetsCountAttribute()
    {
        return $this->assets()->whereNull('deleted_at')->count();
    }
}
