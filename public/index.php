<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Start session
session_start();

// Load configuration
$appConfig = require __DIR__ . '/../config/app.php';
$dbConfig = require __DIR__ . '/../config/database.php';
$routesConfig = require __DIR__ . '/../config/routes.php';

// Error handling based on environment
if ($appConfig['debug']) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Set timezone
date_default_timezone_set($appConfig['timezone']);

// Initialize core components
use App\Core\Router\Router;
use App\Core\Database\Database;
use App\Core\Container\Container;

// Initialize container
$container = Container::getInstance();

// Bind database instance
$container->singleton(Database::class, function() use ($dbConfig) {
    return Database::getInstance($dbConfig);
});

// Initialize router
$router = new Router();

// Load routes
$router->loadRoutes($routesConfig['routes']);

// Dispatch request
try {
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];

    $router->dispatch($method, $uri);
} catch (Exception $e) {
    // Handle errors
    http_response_code(500);

    if ($appConfig['debug']) {
        echo "<h1>Error</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        echo "<h1>Something went wrong</h1>";
        echo "<p>Please try again later.</p>";
    }
}

