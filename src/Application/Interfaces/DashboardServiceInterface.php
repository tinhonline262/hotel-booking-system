<?php

namespace App\Application\Interfaces;

use App\Application\DTOs\DashboardStatsDTO;

interface DashboardServiceInterface
{
    public function getStats(): DashboardStatsDTO;
}