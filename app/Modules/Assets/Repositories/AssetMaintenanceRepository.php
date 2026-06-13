<?php

namespace App\Modules\Assets\Repositories;

use App\Repositories\EloquentRepository;
use App\Modules\Assets\Models\AssetMaintenance;
use App\Modules\Assets\Repositories\Interfaces\AssetMaintenanceRepositoryInterface;

class AssetMaintenanceRepository extends EloquentRepository implements AssetMaintenanceRepositoryInterface
{
    public function __construct(AssetMaintenance $model)
    {
        $this->model = $model;
    }
}
