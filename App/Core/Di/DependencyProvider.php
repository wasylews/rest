<?php

namespace Core\Di;


/**
 * Basic dependency provider.
 * Creates new instance of class for each call
*/
class DependencyProvider {

    protected $class;

    public function __construct(string $class) {
        $this->class = $class;
    }

    /**
     * Get new instance of class by his dependencies
     * @param array $dependencies
     * @return mixed
     */
    public function get(array $dependencies) {
        return new $this->class(...$dependencies);
    }
}