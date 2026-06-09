<?php

namespace App\Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class Department extends Model
{
    use SoftDeletes;

    protected $table = 'departments';
    protected $fillable = ['name', 'description'];

    public function employees()
    {
        return $this->hasMany(User::class, 'department_id');
    }
}
