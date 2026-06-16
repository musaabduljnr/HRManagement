<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class AssetHistory extends Model
{
    protected $table = 'asset_histories';

    protected $fillable = [
        'asset_id', 'performed_by', 'action', 'description', 'old_value', 'new_value'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
