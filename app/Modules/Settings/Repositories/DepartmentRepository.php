<?php

namespace App\Modules\Settings\Repositories;

use App\Repositories\EloquentRepository;
use App\Modules\Settings\Models\Department;
use App\Modules\Settings\Repositories\Interfaces\DepartmentRepositoryInterface;

class DepartmentRepository extends EloquentRepository implements DepartmentRepositoryInterface
{
    public function __construct(Department $model)
    {
        $this->model = $model;
    }
}
