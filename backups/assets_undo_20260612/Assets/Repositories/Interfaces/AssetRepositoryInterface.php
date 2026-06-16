<?php

namespace App\Modules\Assets\Repositories\Interfaces;

interface AssetRepositoryInterface
{
    public function getAvailable();
    public function getWithCategory();
}
