<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class AssetHistory extends Model
{
    protected $table = 'asset_histories';
    protected $guarded = ['id'];
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
