<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class Asset extends Model
{
    use SoftDeletes;

    protected $table = 'assets';

    protected $fillable = [
        'asset_tag', 'name', 'category_id', 'brand', 'model', 'serial_number',
        'description', 'status', 'condition', 'purchase_date', 'purchase_cost',
        'vendor', 'warranty_expiry', 'location', 'notes', 'image'
    ];

    protected $casts = [
        'purchase_date'   => 'date',
        'warranty_expiry' => 'date',
        'purchase_cost'   => 'decimal:2',
    ];

    public static $statuses = [
        'available'   => 'Available',
        'assigned'    => 'Assigned',
        'maintenance' => 'Under Maintenance',
        'retired'     => 'Retired',
        'lost'        => 'Lost / Stolen',
    ];

    public static $conditions = [
        'excellent' => 'Excellent',
        'good'      => 'Good',
        'fair'      => 'Fair',
        'poor'      => 'Poor',
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class, 'asset_id');
    }

    public function activeAssignment()
    {
        return $this->hasOne(AssetAssignment::class, 'asset_id')
                    ->where('status', 'active')
                    ->latest();
    }

    public function histories()
    {
        return $this->hasMany(AssetHistory::class, 'asset_id')->latest();
    }

    public function maintenances()
    {
        return $this->hasMany(AssetMaintenance::class, 'asset_id');
    }

    public function getStatusLabelAttribute()
    {
        return self::$statuses[$this->status] ?? ucfirst($this->status);
    }

    public function getConditionLabelAttribute()
    {
        return self::$conditions[$this->condition] ?? ucfirst($this->condition);
    }

    public function getQrCodeUrlAttribute()
    {
        return route('assets.show', $this->id);
    }

    public function isWarrantyExpiringSoon()
    {
        if (!$this->warranty_expiry) {
            return false;
        }
        return $this->warranty_expiry->diffInDays(now()) <= 30 && $this->warranty_expiry->isFuture();
    }

    public function isWarrantyExpired()
    {
        if (!$this->warranty_expiry) {
            return false;
        }
        return $this->warranty_expiry->isPast();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($asset) {
            if (empty($asset->asset_tag)) {
                $asset->asset_tag = sprintf('AST-%04d', $asset->id);
                $asset->saveQuietly();
            }
        });
    }
}
