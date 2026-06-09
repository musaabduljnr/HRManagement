<?php

namespace App\Modules\Recruitment\Repositories;

use App\Repositories\EloquentRepository;
use App\Modules\Recruitment\Models\JobOpening;
use App\Modules\Recruitment\Repositories\Interfaces\JobOpeningRepositoryInterface;

class JobOpeningRepository extends EloquentRepository implements JobOpeningRepositoryInterface
{
    public function __construct(JobOpening $model)
    {
        $this->model = $model;
    }
}
