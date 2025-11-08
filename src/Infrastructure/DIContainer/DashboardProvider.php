<?php
namespace App\Infrastructure\DIContainer;

use App\Application\Interfaces\DashboardServiceInterface;
use App\Application\Services\DashboardService;
use App\Domain\Interfaces\Repositories\RoomRepositoryInterface;
use App\Domain\Interfaces\Repositories\RoomTypeRepositoryInterface;

class DashboardProvider
{
    public function register($container): void
    {
        $container->singleton(DashboardServiceInterface::class, function($container) {
            return new DashboardService(
                $container->make(RoomRepositoryInterface::class),
                $container->make(RoomTypeRepositoryInterface::class)
            );
        });
    }
}