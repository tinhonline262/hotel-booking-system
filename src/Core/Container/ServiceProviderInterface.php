<?php
namespace App\Core\Container;

interface ServiceProviderInterface
{
    /**
     * Đăng ký các dịch vụ vào container
     * @param Container $container
     */
    public function register(Container $container);
}