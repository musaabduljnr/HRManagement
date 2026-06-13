<?php

namespace App\Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class AssetAssignment extends Model
{
    protected $table = 'asset_assignments';
    protected $guarded = ['id'];
    protected $dates = ['issue_date', 'expected_return_date', 'actual_return_date'];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
