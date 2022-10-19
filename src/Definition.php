<?php

namespace Alaedin\Lab42;

class Definition
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @var bool
     */
    private bool $shared = true;

    /**
     * @var array
     */
    private array $services = [];

    /**
     * @var Definition[]
     */
    private array $dependencies = [];

    /**
     * @param string $id
     * @param bool $shared
     * @param array $services
     * @param array $dependencies
     */
    public function __construct(
        string $id,
        bool $shared = true,
        array $services = [],
        array $dependencies = []
    )
    {
        $this->id = $id;
        $this->shared = $shared;
        $this->services = $services;
        $this->dependencies = $dependencies;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param bool $shared
     * @return self
     */
    public function setShared(bool $shared): self
    {
        $this->shared = $shared;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShared(): bool
    {
        return $this->shared;
    }

}