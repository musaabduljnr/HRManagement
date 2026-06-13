<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMaintenance extends Model
{
    protected $table = 'asset_maintenances';
    protected $guarded = ['id'];
    protected $dates = ['maintenance_date', 'next_maintenance_date'];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
