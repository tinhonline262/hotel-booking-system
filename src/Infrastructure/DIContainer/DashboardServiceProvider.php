<?php
namespace Hotel\Presentation\Controllers\Api; // <-- ĐÚNG NAMESPACE

use Hotel\Application\Interfaces\IDashboardQueryService;

class DashboardController
{
    public function __construct(
        private IDashboardQueryService $dashboardService
    ) {}

    private function jsonResponse(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }

    // ===>>> ĐÃ SỬA TẤT CẢ LỖI 'this.' THÀNH '$this->' <<<===
    
    public function getTodayStats()
    {
        try {
            $data = $this->dashboardService->getTodayStats();
            $this->jsonResponse($data);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function getRoomStatusSummary()
    {
        try {
            $data = $this->dashboardService->getRoomStatusSummary();
            $this->jsonResponse($data);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getRevenueByDateRange()
    {
        try {
            $start = $_GET['start_date'] ?? date('Y-m-01');
            $end = $_GET['end_date'] ?? date('Y-m-t');
            $data = $this->dashboardService->getRevenueByDateRange($start, $end);
            $this->jsonResponse($data);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getMonthlyRevenue()
    {
        try {
            $year = (int)($_GET['year'] ?? date('Y'));
            $month = (int)($_GET['month'] ?? date('m'));
            $data = $this->dashboardService->getMonthlyRevenue($year, $month);
            $this->jsonResponse($data);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function getBookingsByStatus()
    {
        try {
            $data = $this->dashboardService->getBookingsByStatus();
            $this->jsonResponse($data);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getRecentBookings()
    {
        try {
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 10);
            $type = $_GET['type'] ?? null;
            
            $data = $this->dashboardService->getRecentBookings($page, $limit, $type);
            $this->jsonResponse($data);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getOccupancyRate()
    {
         try {
            $data = ['rate' => $this->dashboardService->getOccupancyRate()];
            $this->jsonResponse($data);
        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}