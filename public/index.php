<?php
// =====================================================
// 1️⃣ Xử lý CORS trước tiên (Preflight request)
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Max-Age: 86400');
    http_response_code(204);
    exit;
}

// =====================================================
// 2️⃣ Autoload Composer
// =====================================================
require_once __DIR__ . '/../vendor/autoload.php';

// =====================================================
// 3️⃣ Khởi tạo Session & Load Config
// =====================================================
session_start();

$appConfig = require __DIR__ . '/../config/app.php';
$dbConfig = require __DIR__ . '/../config/database.php';
$routesConfig = require __DIR__ . '/../config/routes.php';

// =====================================================
// 4️⃣ Xử lý lỗi theo môi trường
// =====================================================
if ($appConfig['debug']) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// =====================================================
// 5️⃣ Thiết lập múi giờ
// =====================================================
date_default_timezone_set($appConfig['timezone'] ?? 'UTC');

// =====================================================
// 6️⃣ Khởi tạo Container & Đăng ký Provider
// =====================================================
use Hotel\Core\Container\Container;
use Hotel\Core\Router\Router;
use Hotel\Infrastructure\DIContainer\RepositoryProvider;
use Hotel\Infrastructure\DIContainer\UseCaseProvider;
use Hotel\Infrastructure\DIContainer\ValidatorProvider;
use Hotel\Infrastructure\DIContainer\DatabaseProvider;
use Hotel\Infrastructure\DIContainer\DashboardServiceProvider;

// --- Tạo container ---
$container = new Container();

// --- Đăng ký config vào container (để provider có thể dùng) ---
$container->bind('config', function() use ($appConfig) {
    return $appConfig;
});

$container->bind('db.config', function() use ($dbConfig) {
    return $dbConfig;
});

// --- Đăng ký các service provider của ứng dụng ---
$container->register(new RepositoryProvider());
$container->register(new UseCaseProvider());
$container->register(new ValidatorProvider());
$container->register(new DatabaseProvider());

// 💡 ===>>> THÊM MỚI Ở ĐÂY: Dashboard Provider <<<===
$container->register(new DashboardServiceProvider());
// =====================================================

// =====================================================
// 7️⃣ Khởi tạo Router và nạp routes
// =====================================================
$router = new Router();
$router->setContainer($container);
$router->loadRoutes($routesConfig['routes']);

// =====================================================
// 8️⃣ Dispatch Request
// =====================================================
try {
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = strtok($_SERVER['REQUEST_URI'], '?'); // Bỏ query string

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