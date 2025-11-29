<?php

namespace App\Presentation\Controllers\Api;

use App\Application\Interfaces\DashboardServiceInterface;

class DashboardController extends BaseRestController
{
    private DashboardServiceInterface $dashboardService;

    public function __construct(DashboardServiceInterface $dashboardService)
    {
        parent::__construct();
        $this->dashboardService = $dashboardService;
    }

    public function getStats():void
    {
        try {
            $stats = $this->dashboardService->getStats();
            $this->success($stats->toArray());
        } catch (\Exception $e) {
            $this->error('Failed to fetch dashboard statistics', 500);
        }
    }
}