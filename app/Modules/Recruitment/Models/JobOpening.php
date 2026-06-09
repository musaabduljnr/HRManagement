<?php

namespace App\Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobOpening extends Model
{
    use SoftDeletes;

    protected $table = 'job_openings';

    protected $fillable = [
        'title',
        'description',
        'department_id',
        'status'
    ];

    public function department()
    {
        return $this->belongsTo(\App\Modules\Settings\Models\Department::class, 'department_id');
    }

    public function applications()
    {
        return $this->hasMany(\App\Modules\Recruitment\Models\CandidateApplication::class, 'job_opening_id');
    }
}
