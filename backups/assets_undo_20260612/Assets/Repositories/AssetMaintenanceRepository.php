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

    /**
     * Get all maintenance records for a specific asset.
     */
    public function getForAsset($assetId)
    {
        return $this->model
            ->where('asset_id', $assetId)
            ->whereNull('deleted_at')
            ->orderBy('scheduled_date', 'desc')
            ->get();
    }
}
