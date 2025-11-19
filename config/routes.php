<?php

return [
    'routes' => [
        // ============================================
        // REST API ROUTES - JSON Responses
        // ============================================
        
        // Dashboard API
        ['GET', '/api/dashboard/stats', 'Api\DashboardController@getStats'],

        // Room Types API
        ['GET', '/api/room-types', 'Api\RoomTypeController@index'],
        ['GET', '/api/room-types/filter/capacity', 'Api\RoomTypeController@filterByCapacity'],
        ['GET', '/api/room-types/filter/price', 'Api\RoomTypeController@filterByPriceRange'],
        ['GET', '/api/room-types/{id}', 'Api\RoomTypeController@show'],
        ['POST', '/api/room-types', 'Api\RoomTypeController@create'],
        ['PUT', '/api/room-types/{id}', 'Api\RoomTypeController@update'],
        ['DELETE', '/api/room-types/{id}', 'Api\RoomTypeController@delete'],

        // Room API
        ['GET', '/api/rooms', 'Api\RoomController@index'],
        ['GET', '/api/rooms/filter/status', 'Api\RoomController@filterByStatus'],
        ['GET', '/api/rooms/filter/room-number', 'Api\RoomController@filterByRoomNumber'],
        ['GET', '/api/rooms/{id}', 'Api\RoomController@show'],
        ['POST', '/api/rooms', 'Api\RoomController@create'],
        ['PUT', '/api/rooms/{id}', 'Api\RoomController@update'],
        ['DELETE', '/api/rooms/{id}', 'Api\RoomController@delete'],
        // Booking API
        ['POST', '/api/booking/rooms/{id}', 'Api\BookingController@booking'],
    ],
];
