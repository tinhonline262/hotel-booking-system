<?php

namespace App\Core\Container;

use ReflectionClass;
use ReflectionException;

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
        $concrete = $this->bindings[$abstract]['concrete'] ?? $abstract;
        $singleton = $this->bindings[$abstract]['singleton'] ?? false;

        // Build the instance
        $instance = $this->build($concrete, $parameters);

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
        // If it's a closure, call it
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

        // No constructor, just instantiate
        if ($constructor === null) {
            return new $concrete();
        }

        // Resolve constructor dependencies
        $dependencies = $constructor->getParameters();
        $instances = $this->resolveDependencies($dependencies, $parameters);

        return $reflection->newInstanceArgs($instances);
    }

    /**
     * Resolve method dependencies
     */
    private function resolveDependencies(array $dependencies, array $parameters = []): array
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            $name = $dependency->getName();

            // Use provided parameter if available
            if (isset($parameters[$name])) {
                $results[] = $parameters[$name];
                continue;
            }

            // Try to resolve type-hinted dependency
            $type = $dependency->getType();
            if ($type && !$type->isBuiltin()) {
                $results[] = $this->make($type->getName());
                continue;
            }

            // Use default value if available
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
}

