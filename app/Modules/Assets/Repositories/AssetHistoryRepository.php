<?php

namespace App\Modules\Assets\Repositories;

use App\Repositories\EloquentRepository;
use App\Modules\Assets\Models\AssetHistory;
use App\Modules\Assets\Repositories\Interfaces\AssetHistoryRepositoryInterface;

class AssetHistoryRepository extends EloquentRepository implements AssetHistoryRepositoryInterface
{
    public function __construct(AssetHistory $model)
    {
        $this->model = $model;
    }
}
