<?php

namespace App\Modules\Assets\Repositories;

use App\Repositories\EloquentRepository;
use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Repositories\Interfaces\AssetRepositoryInterface;

class AssetRepository extends EloquentRepository implements AssetRepositoryInterface
{
    public function __construct(Asset $model)
    {
        $this->model = $model;
    }
}
