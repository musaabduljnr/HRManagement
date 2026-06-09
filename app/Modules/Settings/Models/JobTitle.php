<?php

namespace App\Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class JobTitle extends Model
{
    use SoftDeletes;

    protected $table = 'job_titles';
    protected $fillable = ['name', 'description'];

    public function employees()
    {
        return $this->hasMany(User::class, 'job_title_id');
    }
}
