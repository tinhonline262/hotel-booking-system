<?php

return [
    'routes' => [
        // Home
        ['GET', '/', 'HomeController@index'],
        ['GET', '/about', 'HomeController@about'],
        ['GET', '/contact', 'HomeController@contact'],
        ['POST', '/contact', 'HomeController@submitContact'],

        // Rooms
        ['GET', '/rooms', 'RoomController@index'],
        ['GET', '/rooms/{id}', 'RoomController@show'],
        ['GET', '/rooms/search', 'RoomController@search'],

        // Bookings
        ['GET', '/booking/create', 'BookingController@create'],
        ['POST', '/booking/store', 'BookingController@store'],
        ['GET', '/booking/confirmation/{id}', 'BookingController@confirmation'],
        ['GET', '/booking/cancel/{id}', 'BookingController@cancel'],

        // Auth
        ['GET', '/login', 'AuthController@showLogin'],
        ['POST', '/login', 'AuthController@login'],
        ['GET', '/register', 'AuthController@showRegister'],
        ['POST', '/register', 'AuthController@register'],
        ['POST', '/logout', 'AuthController@logout'],

        // User Dashboard
        ['GET', '/dashboard', 'DashboardController@index'],
        ['GET', '/dashboard/bookings', 'DashboardController@bookings'],
        ['GET', '/dashboard/profile', 'DashboardController@profile'],
        ['POST', '/dashboard/profile', 'DashboardController@updateProfile'],

        // Admin
        ['GET', '/admin', 'AdminController@index'],
        ['GET', '/admin/rooms', 'AdminRoomController@index'],
        ['GET', '/admin/rooms/create', 'AdminRoomController@create'],
        ['POST', '/admin/rooms/store', 'AdminRoomController@store'],
        ['GET', '/admin/rooms/{id}/edit', 'AdminRoomController@edit'],
        ['POST', '/admin/rooms/{id}/update', 'AdminRoomController@update'],
        ['POST', '/admin/rooms/{id}/delete', 'AdminRoomController@delete'],
        ['GET', '/admin/bookings', 'AdminBookingController@index'],
        ['GET', '/admin/bookings/{id}', 'AdminBookingController@show'],
        ['POST', '/admin/bookings/{id}/approve', 'AdminBookingController@approve'],
        ['POST', '/admin/bookings/{id}/reject', 'AdminBookingController@reject'],
    ],
];

