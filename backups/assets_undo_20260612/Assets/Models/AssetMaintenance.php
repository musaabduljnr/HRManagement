<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class AssetMaintenance extends Model
{
    use SoftDeletes;

    protected $table = 'asset_maintenances';

    protected $fillable = [
        'asset_id', 'title', 'type', 'status', 'scheduled_date',
        'completed_date', 'cost', 'vendor', 'description', 'findings', 'created_by'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'cost'           => 'decimal:2',
    ];

    public static $types = [
        'preventive' => 'Preventive',
        'corrective' => 'Corrective',
        'inspection' => 'Inspection',
        'upgrade'    => 'Upgrade',
    ];

    public static $statuses = [
        'scheduled'   => 'Scheduled',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
        'cancelled'   => 'Cancelled',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
