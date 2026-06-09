<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HrPolicy extends Model
{
    protected $table = 'hr_policies';

    protected $fillable = [
        'title',
        'content',
        'category'
    ];
}
