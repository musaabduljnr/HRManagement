<?php

namespace App\Modules\Pim\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollRecord extends Model
{
    use SoftDeletes;

    protected $table = 'payroll_records';

    protected $fillable = [
        'user_id',
        'payroll_month',
        'base_salary',
        'allowances',
        'deductions',
        'bonuses',
        'net_salary',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function payslips()
    {
        return $this->hasMany(\App\Modules\Pim\Models\Payslip::class, 'payroll_record_id');
    }
}
