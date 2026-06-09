<?php

namespace App\Modules\Pim\Repositories;

use App\Repositories\EloquentRepository;
use App\Modules\Pim\Models\PayrollRecord;
use App\Modules\Pim\Repositories\Interfaces\PayrollRecordRepositoryInterface;

class PayrollRecordRepository extends EloquentRepository implements PayrollRecordRepositoryInterface
{
    public function __construct(PayrollRecord $model)
    {
        $this->model = $model;
    }
}
