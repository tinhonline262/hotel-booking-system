<?php
namespace App\Infrastructure\DIContainer; 

use App\Core\Container\Container;
use App\Core\Container\ServiceProviderInterface; 
use App\Presentation\Controllers\HomeController;
use App\Application\UseCases\GetFeaturedRoomTypesUseCase;
use App\Core\Template\ITemplateEngine; 
use App\Core\Template\TemplateEngine;  

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
            );
        });
    }
}