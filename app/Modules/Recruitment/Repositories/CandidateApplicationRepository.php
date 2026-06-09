<?php

namespace App\Modules\Recruitment\Repositories;

use App\Repositories\EloquentRepository;
use App\Modules\Recruitment\Models\CandidateApplication;
use App\Modules\Recruitment\Repositories\Interfaces\CandidateApplicationRepositoryInterface;

class CandidateApplicationRepository extends EloquentRepository implements CandidateApplicationRepositoryInterface
{
    public function __construct(CandidateApplication $model)
    {
        $this->model = $model;
    }
}
