<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class AssetAssignment extends Model
{
    protected $table = 'asset_assignments';

    protected $fillable = [
        'asset_id', 'user_id', 'assigned_by', 'assigned_date',
        'expected_return_date', 'actual_return_date', 'status',
        'condition_at_assignment', 'condition_at_return',
        'notes', 'return_notes'
    ];

    protected $casts = [
        'assigned_date'        => 'date',
        'expected_return_date' => 'date',
        'actual_return_date'   => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
