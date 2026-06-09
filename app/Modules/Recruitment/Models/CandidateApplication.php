<?php

namespace App\Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateApplication extends Model
{
    use SoftDeletes;

    protected $table = 'candidates';

    protected $fillable = [
        'user_id',
        'job_opening_id',
        'status',
        'resume_path',
        'notes'
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function jobOpening()
    {
        return $this->belongsTo(\App\Modules\Recruitment\Models\JobOpening::class, 'job_opening_id');
    }

    public function interviews()
    {
        return $this->hasMany(\App\Modules\Recruitment\Models\Interview::class, 'candidate_id');
    }
}
