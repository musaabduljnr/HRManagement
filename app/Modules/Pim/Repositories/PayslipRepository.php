<?php

namespace App\Modules\Pim\Repositories;

use App\Repositories\EloquentRepository;
use App\Modules\Pim\Models\Payslip;
use App\Modules\Pim\Repositories\Interfaces\PayslipRepositoryInterface;

class PayslipRepository extends EloquentRepository implements PayslipRepositoryInterface
{
    public function __construct(Payslip $model)
    {
        $this->model = $model;
    }
}
