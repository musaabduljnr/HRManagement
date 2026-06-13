<?php

namespace App\Modules\Assets\Repositories;

use App\Repositories\EloquentRepository;
use App\Modules\Assets\Models\AssetCategory;
use App\Modules\Assets\Repositories\Interfaces\AssetCategoryRepositoryInterface;

class AssetCategoryRepository extends EloquentRepository implements AssetCategoryRepositoryInterface
{
    public function __construct(AssetCategory $model)
    {
        $this->model = $model;
    }
}
