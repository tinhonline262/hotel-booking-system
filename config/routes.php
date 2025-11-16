<?php

return [
    'routes' => [
        // ============================================
        // REST API ROUTES - JSON Responses
        // ============================================

        // Room Types API
        ['GET', '/api/room-types', 'Api\RoomTypeController@index'],
        ['GET', '/api/room-types/filter/capacity', 'Api\RoomTypeController@filterByCapacity'],
        ['GET', '/api/room-types/filter/price', 'Api\RoomTypeController@filterByPriceRange'],
        ['GET', '/api/room-types/{id}', 'Api\RoomTypeController@show'],
        ['POST', '/api/room-types', 'Api\RoomTypeController@create'],
        ['PUT', '/api/room-types/{id}', 'Api\RoomTypeController@update'],
        ['DELETE', '/api/room-types/{id}', 'Api\RoomTypeController@delete'],

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
