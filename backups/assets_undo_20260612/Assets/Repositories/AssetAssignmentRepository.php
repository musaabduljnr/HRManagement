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

    /**
     * Get all active assignments for a given employee.
     */
    public function getActiveForEmployee($userId)
    {
        return $this->model
            ->with('asset.category')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->orderBy('assigned_date', 'desc')
            ->get();
    }

    /**
     * Get full assignment history for an employee.
     */
    public function getHistoryForEmployee($userId)
    {
        return $this->model
            ->with('asset.category', 'assignedBy')
            ->where('user_id', $userId)
            ->orderBy('assigned_date', 'desc')
            ->get();
    }
}
