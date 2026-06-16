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

    /**
     * Get all available assets (not assigned/retired/lost).
     */
    public function getAvailable()
    {
        return $this->model->where('status', 'available')->whereNull('deleted_at')->get();
    }

    /**
     * Get all assets with their category eager-loaded.
     */
    public function getWithCategory()
    {
        return $this->model->with('category')->whereNull('deleted_at')->get();
    }

    /**
     * Get the datatable query with category join.
     */
    public function getDatatableQuery()
    {
        return $this->model->newQuery()
            ->with('category', 'activeAssignment.employee')
            ->whereNull('assets.deleted_at');
    }

    /**
     * Get dashboard summary counts.
     */
    public function getDashboardCounts()
    {
        return [
            'total'       => $this->model->whereNull('deleted_at')->count(),
            'available'   => $this->model->where('status', 'available')->whereNull('deleted_at')->count(),
            'assigned'    => $this->model->where('status', 'assigned')->whereNull('deleted_at')->count(),
            'maintenance' => $this->model->where('status', 'maintenance')->whereNull('deleted_at')->count(),
            'retired'     => $this->model->where('status', 'retired')->whereNull('deleted_at')->count(),
        ];
    }

    public function countByStatus($status)
    {
        return $this->model->where('status', $status)->whereNull('deleted_at')->count();
    }
}
