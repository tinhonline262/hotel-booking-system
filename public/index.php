<?php

// Handle CORS preflight BEFORE anything else
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Max-Age: 86400');
    http_response_code(204);
    exit;
}

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
use App\Core\Container\Container;
use App\Core\Router\Router;
use App\Infrastructure\DIContainer\AppServiceProvider;

// Initialize container
$container = Container::getInstance();

// Register all service providers through AppServiceProvider
AppServiceProvider::register($container);

// Boot providers (for any post-registration logic)
AppServiceProvider::boot($container);

// Initialize router
$router = new Router();

// Set container on router for dependency injection
$router->setContainer($container);

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
