<?php

namespace App\Modules\Assets\Repositories\Interfaces;

interface AssetAssignmentRepositoryInterface
{
    public function getActiveForEmployee($userId);
    public function getHistoryForEmployee($userId);
}
