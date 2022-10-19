<?php

namespace Alaedin\Lab42;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;
use ReflectionException;

class Container implements ContainerInterface
{
    /**
     * @var Definition[]
     */
    private array $definitions = [];

    /**
     * @var array
     */
    private array $instances = [];

    /**
     * @var array
     */
    private array $services = [];


    /**
     * @param string $id
     * @return $this
     * @throws ReflectionException
     */
    public function register(string $id): self
    {
        $reflection = new ReflectionClass($id);

        if ($reflection->isInterface()) {
            $this->register($this->services[$id]);
            $this->definitions[$id] = &$this->definitions[$this->services[$id]];
            return $this;
        }

        $dependencies = [];

        if ($reflection->getConstructor() !== null) {

            $dependencies = array_map(
                fn(ReflectionParameter $param) => $this->getDefinition($param->getType()->getName()),
                $reflection->getConstructor()->getParameters()
            );
        }

        $services = array_filter(
            $this->services,
            fn(string $service) => $id == $service
        );

        $definition = new Definition(
            $id,
            true,
            $services,
            $dependencies
        );

        $this->definitions[$id] = $definition;

        return $this;
    }

    /**
     * @param string $id
     * @return Definition
     * @throws ReflectionException
     */
    public function getDefinition(string $id): Definition
    {
        if (!isset($this->definitions[$id])) {
            $this->register($id);
        }

        return $this->definitions[$id];
    }

    /**
     * @param string $id
     * @return mixed
     * @throws ReflectionException
     */
    public function get(string $id): mixed
    {
        if (!$this->has($id)) {

            $instance = $this->resolve($id);

            if (!$this->getDefinition($id)->isShared()) {
                return $instance;
            }

            $this->instances[$id] = $instance;
        }

        return $this->instances[$id];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->instances[$id]);
    }

    /**
     * @param string $id
     * @param string $class
     * @return self
     */
    public function addService(string $id, string $class): self
    {
        $this->services[$id] = $class;

        return $this;
    }

    /**
     * @param string $id
     * @return object
     * @throws ReflectionException
     */
    private function resolve(string $id): object
    {
        $definition = $this->getDefinition($id);

        $reflection = new ReflectionClass($definition->getId());

        if ($reflection->isInterface()) {
            return $this->resolve($definition->getId());
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $params = $constructor->getParameters();

        return $reflection->newInstanceArgs(
            array_map(
                fn(ReflectionParameter $param) => $this->get($param->getType()->getName()),
                $params
            )
        );
    }
}