<?php
namespace App\Infrastructure\DIContainer;

use App\Core\Container\Container;
use App\Core\Container\ServiceProviderInterface;
use App\Core\Database\IDatabaseConnection;
use App\Application\Interfaces\ICacheService;
use App\Application\Services\FileSystemCacheService;
use App\Application\Interfaces\IDashboardQueryService;
use App\Application\Services\MySqlDashboardQueryService;
use App\Application\Services\CachedDashboardQueryService;
use App\Presentation\Controllers\Api\DashboardController;

class DashboardServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {

        $config = $container->make('config'); 
        $cachePath = $config['paths']['cache']; 

        $container->bind(ICacheService::class, function($c) use ($cachePath) {
            return new FileSystemCacheService($cachePath);
        });

        $container->bind(IDashboardQueryService::class, function($c) {
            $realService = new MySqlDashboardQueryService(
                $c->make(IDatabaseConnection::class) 
            );
            $cachedService = new CachedDashboardQueryService(
                $realService, 
                $c->make(ICacheService::class) 
            );
            return $cachedService;
        });

        $container->bind(DashboardController::class, function($c) { 
            return new DashboardController( 
                $c->make(IDashboardQueryService::class) 
            );
        });
    }
}