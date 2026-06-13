<?php

namespace App\Modules\Assets\Repositories;

use App\Repositories\EloquentRepository;
use App\Modules\Assets\Models\AssetAssignment;
use App\Modules\Assets\Repositories\Interfaces\AssetAssignmentRepositoryInterface;

class AssetAssignmentRepository extends EloquentRepository implements AssetAssignmentRepositoryInterface
{
    public function __construct(AssetAssignment $model)
    {
        $this->model = $model;
    }
}
