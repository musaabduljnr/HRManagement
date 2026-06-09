<?php

namespace App\Modules\Recruitment\Repositories;

use App\Repositories\EloquentRepository;
use App\Modules\Recruitment\Models\Interview;
use App\Modules\Recruitment\Repositories\Interfaces\InterviewRepositoryInterface;

class InterviewRepository extends EloquentRepository implements InterviewRepositoryInterface
{
    public function __construct(Interview $model)
    {
        $this->model = $model;
    }
}
