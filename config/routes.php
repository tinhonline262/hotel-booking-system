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
        ['GET', '/api/rooms/details', 'Api\RoomController@indexWithDetails'],
        ['GET', '/api/rooms/filter/status', 'Api\RoomController@filterByStatus'],
        ['GET', '/api/rooms/filter/room-number', 'Api\RoomController@filterByRoomNumber'],
        ['GET', '/api/rooms/{id}', 'Api\RoomController@show'],
        ['GET', '/api/rooms/{id}/details', 'Api\RoomController@showWithDetails'],
        ['POST', '/api/rooms', 'Api\RoomController@create'],
        ['PUT', '/api/rooms/{id}', 'Api\RoomController@update'],
        ['DELETE', '/api/rooms/{id}', 'Api\RoomController@delete'],


        // Search API - Available rooms only
        ['GET', '/api/search/rooms', 'Api\SearchController@searchRooms'],
        ['GET', '/api/search/rooms/available', 'Api\SearchController@getAvailableRooms'],
        ['GET', '/api/search/rooms/dates', 'Api\SearchController@searchWithDates'],

        // Booking API
        ['POST', '/api/booking/rooms/{id}', 'Api\BookingController@booking'],
        ['GET', '/api/booking/rooms/{id}', 'Api\BookingController@check'],
        //CRUD Booking API
        ['GET', '/api/bookings', 'Api\CRUDbookingController@index'],
        ['GET', '/api/bookings/filter/checkin', 'Api\CRUDbookingController@filterByCheckIn'],
        ['GET', '/api/bookings/filter/checkout', 'Api\CRUDbookingController@filterByCheckOut'],
        ['GET', '/api/bookings/filter/code', 'Api\CRUDbookingController@filterByCode'],
        ['GET', '/api/bookings/search/code/{code}', 'Api\CRUDbookingController@findByCode'],
        ['GET', '/api/bookings/filter/email', 'Api\CRUDbookingController@filterByEmail'],
        ['GET', '/api/bookings/filter/name', 'Api\CRUDbookingController@filterByName'],
        ['GET', '/api/bookings/filter/phone', 'Api\CRUDbookingController@filterByPhone'],
        ['GET', '/api/bookings/filter/status', 'Api\CRUDbookingController@filterByStatus'],
        ['GET', '/api/bookings/{id}', 'Api\CRUDbookingController@show'],
        ['PUT', '/api/bookings/{id}', 'Api\CRUDbookingController@update'],
        ['DELETE', '/api/bookings/{id}', 'Api\CRUDbookingController@delete'],
        //RoomDetailController
        ['GET', '/api/room-details/{id}', 'Api\RoomDetailController@getDetailRoom'],
        // Room Images API
        ['POST', '/api/rooms/{id}/images', 'Api\RoomImageController@upload'],
        ['PUT', '/api/rooms/{roomId}/images/{imageId}/primary', 'Api\RoomImageController@setPrimary'],
        ['PUT', '/api/rooms/images/order', 'Api\RoomImageController@updateOrder'],
        ['DELETE', '/api/rooms/images/{id}', 'Api\RoomImageController@delete'],

        // Storage Management API
        ['GET', '/api/storage/health', 'Api\RoomImageController@healthCheck'],
        ['GET', '/api/storage/info', 'Api\RoomImageController@storageInfo'],
        ['PUT', '/api/storage/provider', 'Api\RoomImageController@switchProvider'],
    ],
];
