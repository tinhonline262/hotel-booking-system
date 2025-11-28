<?php
namespace App\Core\Container;

use ReflectionClass;
use ReflectionException;
use App\Core\Container\ServiceProviderInterface; // <-- THÊM DÒNG NÀY

/**
 * Dependency Injection Container
 */
class Container
{
    private static ?Container $instance = null;
    private array $bindings = [];
    private array $instances = [];

    private function __construct() {}

    /**
     * Get singleton instance
     */
    public static function getInstance(): Container
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Bind an interface to implementation
     */
    public function bind(string $abstract, $concrete = null, bool $singleton = false): void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        // Code bind() gốc của bạn đã đúng
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'singleton' => $singleton,
        ];
    }

    /**
     * Bind as singleton
     */
    public function singleton(string $abstract, $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Resolve a dependency
     */
    public function make(string $abstract, array $parameters = [])
    {
        // Return existing singleton instance
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // Get binding or use abstract as concrete
        $binding = $this->bindings[$abstract] ?? null;
        $concrete = $binding['concrete'] ?? $abstract;
        $singleton = $binding['singleton'] ?? false;
        
        // Build the instance
        // === SỬA LỖI LOGIC NHỎ ===
        // Chuyển $concrete (tên class) thành $binding['concrete'] (có thể là Closure)
        $instance = $this->build($binding['concrete'] ?? $concrete, $parameters);

        // Store singleton
        if ($singleton) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Build an instance with dependency injection
     */
    private function build($concrete, array $parameters = [])
    {
        // (Code build() gốc của bạn đã đúng)
        if ($concrete instanceof \Closure) {
            return $concrete($this);
        }
        try {
            $reflection = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new \Exception("Class {$concrete} does not exist");
        }
        if (!$reflection->isInstantiable()) {
            throw new \Exception("Class {$concrete} is not instantiable");
        }
        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            return new $concrete();
        }
        $dependencies = $constructor->getParameters();
        $instances = $this->resolveDependencies($dependencies, $parameters);
        return $reflection->newInstanceArgs($instances);
    }

    /**
     * Resolve method dependencies
     */
    private function resolveDependencies(array $dependencies, array $parameters = []): array
    {
        // (Code resolveDependencies() gốc của bạn đã đúng)
        $results = [];
        foreach ($dependencies as $dependency) {
            $name = $dependency->getName();
            if (isset($parameters[$name])) {
                $results[] = $parameters[$name];
                continue;
            }
            $type = $dependency->getType();
            if ($type && !$type->isBuiltin()) {
                $results[] = $this->make($type->getName());
                continue;
            }
            if ($dependency->isDefaultValueAvailable()) {
                $results[] = $dependency->getDefaultValue();
                continue;
            }
            throw new \Exception("Cannot resolve dependency: {$name}");
        }
        return $results;
    }

    /**
     * Set an instance directly
     */
    public function instance(string $abstract, $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    // ===>>> (PHƯƠNG THỨC BỊ THIẾU) <<<===
    /**
     * Đăng ký một Service Provider
     */
    public function register(ServiceProviderInterface $provider)
    {
        // Gọi phương thức register() của Provider
        // và truyền chính container này vào
        $provider->register($this);
    }
}