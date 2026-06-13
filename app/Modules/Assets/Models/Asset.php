<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $table = 'assets';
    protected $guarded = ['id'];
    protected $dates = ['deleted_at', 'purchase_date', 'warranty_expiry'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->asset_code)) {
                $latest = static::withTrashed()->orderBy('id', 'desc')->first();
                $nextId = $latest ? $latest->id + 1 : 1;
                $model->asset_code = 'AST-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class, 'asset_id');
    }

    public function currentAssignment()
    {
        return $this->hasOne(AssetAssignment::class, 'asset_id')->where('status', 'Active');
    }

    public function maintenances()
    {
        return $this->hasMany(AssetMaintenance::class, 'asset_id');
    }

    public function histories()
    {
        return $this->hasMany(AssetHistory::class, 'asset_id')->orderBy('created_at', 'desc');
    }

    public function getQrCodeUrlAttribute()
    {
        $url = route('assets.list.show', $this->id);
        return 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($url);
    }
}
