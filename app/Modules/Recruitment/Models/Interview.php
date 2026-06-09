<?php

namespace App\Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use SoftDeletes;

    protected $table = 'interviews';

    protected $fillable = [
        'candidate_id',
        'interview_date',
        'interviewer_name',
        'notes',
        'status'
    ];

    public function candidate()
    {
        return $this->belongsTo(\App\Modules\Recruitment\Models\CandidateApplication::class, 'candidate_id');
    }
}
