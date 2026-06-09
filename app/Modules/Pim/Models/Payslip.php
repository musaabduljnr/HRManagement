<?php

namespace App\Modules\Pim\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payslip extends Model
{
    use SoftDeletes;

    protected $table = 'payslips';

    protected $fillable = [
        'payroll_record_id',
        'payslip_number',
        'pdf_path'
    ];

    public function payrollRecord()
    {
        return $this->belongsTo(\App\Modules\Pim\Models\PayrollRecord::class, 'payroll_record_id');
    }
}
