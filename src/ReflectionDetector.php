<?php

namespace Naoray\LaravelFactoryPrefill;

use ReflectionClass;
use ReflectionObject;

/**
 * @todo
 * - add discover by return types as strategies
 * - explore other methods and add filter to own collection class
 */
class ReflectionDetector
{
    protected $resource;

    public function __construct($resource)
    {
        $this->resource = is_object($resource)
            ? new ReflectionObject($resource)
            : new ReflectionClass($resource);
    }

    public function getMethods()
    {
        return collect($this->resource->getMethods())
            ->mapInto(ReflectionMethod::class);
    }
}
