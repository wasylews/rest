<?php

namespace core\di;


class DependencyContainer {

    /**
     * @field DependencyProvider[] $providers
    */
    private $providers = [];

    /**
     * Register new class in container
     * @param $class
     */
    public function register($class) {
        $this->providers[$class] = new DependencyProvider($class);
    }

    /**
     * Get class instance from container
     * @param $class
     * @return mixed|null get instance or null if there is no provider for class
     */
    public function get($class) {
        if ($this->has($class)) {
            try {
                return $this->providers[$class]->get($this->resolveDependencies($class));
            } catch (DependencyException $e) {
                return null;
            }
        }
        return null;
    }

    public function has($class) {
        return array_key_exists($class, $this->providers);
    }

    /**
     * Collect all dependencies for constructor of class
     * @param $class
     * @return array
     * @throws DependencyException if it can't find provider for parameter in this container
     */
    private function resolveDependencies($class): array {
        $dependencies = [];
        $parameters = $this->getConstructorParameters($class);
        foreach ($parameters as $parameter) {
            if (!$this->has($parameter)) {
                throw new DependencyException("No provider available for $parameter");
            } else {
                array_push($dependencies, $this->get($parameter));
            }
        }
        return $dependencies;
    }

    /**
     * Collect types list of constructor parameters
     * @param $class
     * @return array
     * @throws DependencyException if parameter resolving failed
     */
    private function getConstructorParameters($class): array {
        $parameters = [];
        try {
            $reflectionClass = new \ReflectionClass($class);
            $constructor = $reflectionClass->getConstructor();
            if ($constructor != null) {
                foreach ($constructor->getParameters() as $parameter) {
                    if ($parameter->getType()->isBuiltin() && !$parameter->isDefaultValueAvailable()) {
                        throw new DependencyException(sprintf('No default value available for %s in %s::%s()',
                            $parameter->getName(),
                            $parameter->getDeclaringClass()->getName(),
                            $parameter->getDeclaringFunction()->getName()
                        ));
                    } else {
                        array_push($parameters, $parameter->getClass()->getName());
                    }
                }
            }
        } catch (\ReflectionException $e) {
        }
        return $parameters;
    }


}