<?php
namespace App\Infrastructure\DIContainer; 

use App\Core\Container\Container;
use App\Core\Container\ServiceProviderInterface; 
use App\Presentation\Controllers\HomeController;
use App\Application\UseCases\GetFeaturedRoomTypesUseCase;
use App\Core\Template\ITemplateEngine; 
use App\Core\Template\TemplateEngine;  
namespace App\Infrastructure\DIContainer;

use App\Application\Interfaces\BookingServiceInterface;
use App\Application\Interfaces\RoomServiceInterface;
use App\Application\Services\BookingService;
use App\Application\Services\RoomService;
use App\Application\Services\RoomTypeService;
use App\Application\Interfaces\RoomImageServiceInterface;
use App\Core\Container\Container;
use App\Domain\Interfaces\Repositories\BookingRepositoryInterface;
use App\Presentation\Controllers\Api\BookingController;
use App\Presentation\Controllers\Api\CRUDbookingController;
use App\Presentation\Controllers\Api\RoomController;
use App\Presentation\Controllers\Api\RoomDetailController;
use App\Presentation\Controllers\Api\RoomImageController;
use App\Presentation\Controllers\Api\RoomTypeController;
use App\Presentation\Controllers\Api\SearchController;

class ControllerProvider implements ServiceProviderInterface 
{
    public function register(Container $container)
    {
        $container->bind(ITemplateEngine::class, function($c) {
            $config = $c->make('config');
            $viewsPath = $config['paths']['views'] ?? __DIR__ . '/../../Presentation/Views'; 
            
            return new TemplateEngine($viewsPath);
        });

        $container->bind(HomeController::class, function($c) {
            return new HomeController(
                $c->make(GetFeaturedRoomTypesUseCase::class), 
                $c->make(ITemplateEngine::class) 
        // RoomImage Controller
        $container->bind(RoomImageController::class, function (Container $c) {
            return new RoomImageController(
                $c->make(RoomImageServiceInterface::class)
            );
        });

        // Add more controllers here
        // UserController, BookingController, AuthController, etc.
        $container->bind(RoomController::class, function (Container $c) {
            return new RoomController(
                $c->make(RoomService::class)
            );
        });
        $container->bind(BookingController::class, function (Container $c) {
            return new BookingController(
                $c->make(BookingService::class)
            );
        });

        $container->bind(CRUDbookingController::class, function (Container $c) {
            return new CRUDbookingController(
                $c->make(BookingService::class)
            );
        });

        $container->bind(RoomDetailController::class, function (Container $c) {
            return new RoomDetailController(
                $c->make(RoomService::class)
            );
        });

        $container->bind(SearchController::class, function (Container $c) {
            return new SearchController(
                $c->make(RoomService::class),
                $c->make(BookingRepositoryInterface::class)
            );
        });
    }
}