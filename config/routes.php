<?php

return [
    'routes' => [
        // ============================================
        // REST API ROUTES - JSON Responses
        // ============================================

        // 🏨 Room Types API
        ['GET', '/api/room-types', 'Api\RoomTypeController@index'],
        ['GET', '/api/room-types/filter/capacity', 'Api\RoomTypeController@filterByCapacity'],
        ['GET', '/api/room-types/filter/price', 'Api\RoomTypeController@filterByPriceRange'],
        ['GET', '/api/room-types/{id}', 'Api\RoomTypeController@show'],
        ['POST', '/api/room-types', 'Api\RoomTypeController@create'],
        ['PUT', '/api/room-types/{id}', 'Api\RoomTypeController@update'],
        ['DELETE', '/api/room-types/{id}', 'Api\RoomTypeController@delete'],

        // ============================================
        // 📊 DASHBOARD API ROUTES
        // ============================================
        // (Giả sử bạn đã tạo DashboardController)
       ['GET', '/api/dashboard/stats/today', 'Api\DashboardController@getTodayStats'],
        ['GET', '/api/dashboard/stats/room-summary', 'Api\DashboardController@getRoomStatusSummary'],
        ['GET', '/api/dashboard/stats/booking-status', 'Api\DashboardController@getBookingsByStatus'],
        ['GET', '/api/dashboard/stats/occupancy', 'Api\DashboardController@getOccupancyRate'],
        ['GET', '/api/dashboard/charts/revenue-range', 'Api\DashboardController@getRevenueByDateRange'],
        ['GET', '/api/dashboard/charts/monthly-revenue', 'Api\DashboardController@getMonthlyRevenue'],
        ['GET', '/api/dashboard/tables/recent-bookings', 'Api\DashboardController@getRecentBookings'],
        
    ],
];
