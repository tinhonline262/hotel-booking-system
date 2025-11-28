<?php

//  Xử lý CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Max-Age: 86400');
    http_response_code(204);
    exit;
}

// Autoload Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Khởi tạo Session & Load Config
session_start();
$appConfig = require __DIR__ . '/../config/app.php';
$dbConfig = require __DIR__ . '/../config/database.php';
$routesConfig = require __DIR__ . '/../config/routes.php';

// Xử lý lỗi
if ($appConfig['debug']) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Múi giờ
date_default_timezone_set($appConfig['timezone'] ?? 'UTC');

// Khởi tạo Container & Đăng ký Provider
use App\Core\Container\Container;
use App\Core\Router\Router;
use App\Infrastructure\DIContainer\RepositoryProvider;
use App\Infrastructure\DIContainer\UseCaseProvider;
use App\Infrastructure\DIContainer\ValidatorProvider;
use App\Infrastructure\DIContainer\DashboardServiceProvider;
use App\Infrastructure\DIContainer\ControllerProvider;

$container = Container::getInstance();

// --- Đăng ký config vào container ---
$container->instance('config', $appConfig);
$container->instance('db.config', $dbConfig);

$dbProviderClass = 'App\\Infrastructure\\DIContainer\\DatabaseServiceProvider';
// Check for the class at runtime to avoid "undefined type" static/compile errors
if (class_exists($dbProviderClass)) {
    (new $dbProviderClass())->register($container); // (Nên đăng ký CSDL trước)
} else {
    // If the provider class is missing, log and continue so the app doesn't fatal here.
    // Adjust this behavior if you want to fail fast instead.
    error_log("Missing DI provider: {$dbProviderClass}");
}
(new RepositoryProvider())->register($container);
(new UseCaseProvider())->register($container);
(new ValidatorProvider())->register($container);
(new DashboardServiceProvider())->register($container);
(new ControllerProvider())->register($container);
// 7 Khởi tạo Router và nạp routes
$router = $container->make(Router::class); 
$router->loadRoutes($routesConfig['routes']);


//  Dispatch Request
try {
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = strtok($_SERVER['REQUEST_URI'], '?'); 

    $router->dispatch($method, $uri);

} catch (Exception $e) {
    http_response_code(500);

    if ($appConfig['debug']) {
        echo "<h1>Error</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        echo "<h1>Something went wrong</h1>";
        echo "<p>Please try again later.</p>";
    }
}