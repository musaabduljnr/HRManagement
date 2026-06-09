<?php

namespace App\Modules\Settings\Repositories;

use App\Repositories\EloquentRepository;
use App\Modules\Settings\Models\JobTitle;
use App\Modules\Settings\Repositories\Interfaces\JobTitleRepositoryInterface;

class JobTitleRepository extends EloquentRepository implements JobTitleRepositoryInterface
{
    public function __construct(JobTitle $model)
    {
        $this->model = $model;
    }
}
