<?php

namespace App\Presentation\Controllers\Api;

use App\Application\Interfaces\DashboardServiceInterface;

class DashboardController extends BaseRestController
{
    private DashboardServiceInterface $dashboardService;

    public function __construct(DashboardServiceInterface $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function getStats()
    {
        try {
            $stats = $this->dashboardService->getStats();
            return $this->success($stats->toArray());
        } catch (\Exception $e) {
            return $this->error('Failed to fetch dashboard statistics', 500);
        }
    }
}