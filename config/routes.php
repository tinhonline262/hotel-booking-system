<?php

return [
    'routes' => [
        // ============================================
        // 🌐 WEBSITE ROUTES (trang web người dùng)
        // ============================================
        // Trang chủ
        ['GET', '/', 'HomeController@index'],
        //  Trang tìm kiếm phòng (sẽ làm sau)
        ['GET', '/search', 'SearchController@handleSearch'],
        // ============================================
        // REST API ROUTES - JSON Responses
        // ============================================
        //  Room Types API
        ['GET', '/api/room-types', 'Api\RoomTypeController@index'],
        ['GET', '/api/room-types/filter/capacity', 'Api\RoomTypeController@filterByCapacity'],
        ['GET', '/api/room-types/filter/price', 'Api\RoomTypeController@filterByPriceRange'],
        ['GET', '/api/room-types/{id}', 'Api\RoomTypeController@show'],
        ['POST', '/api/room-types', 'Api\RoomTypeController@create'],
        ['PUT', '/api/room-types/{id}', 'Api\RoomTypeController@update'],
        ['DELETE', '/api/room-types/{id}', 'Api\RoomTypeController@delete'],

        // (Tùy chọn) API mới để lấy danh sách loại phòng nổi bật
        ['GET', '/api/room-types/featured', 'Api\RoomTypeController@getFeatured'],

        // ============================================
        // DASHBOARD API ROUTES
        // ============================================

        // Thống kê cho Dashboard quản trị
        ['GET', '/api/dashboard/today-stats', 'Api\DashboardController@getTodayStats'],
        ['GET', '/api/dashboard/room-summary', 'Api\DashboardController@getRoomStatusSummary'],
        ['GET', '/api/dashboard/revenue/date-range', 'Api\DashboardController@getRevenueByDateRange'],
        ['GET', '/api/dashboard/revenue/monthly', 'Api\DashboardController@getMonthlyRevenue'],
        ['GET', '/api/dashboard/bookings/status', 'Api\DashboardController@getBookingsByStatus'],
        ['GET', '/api/dashboard/bookings/recent', 'Api\DashboardController@getRecentBookings'],
        ['GET', '/api/dashboard/occupancy-rate', 'Api\DashboardController@getOccupancyRate'],
    ],
];
