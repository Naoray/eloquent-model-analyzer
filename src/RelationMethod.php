<?php

namespace Naoray\EloquentModelAnalyzer;

use Illuminate\Support\Traits\ForwardsCalls;

class RelationMethod
{
    use ForwardsCalls;

    protected $resource;

    public function __construct(\ReflectionMethod $method)
    {
        $this->resource = $method;
    }

    public function toArray()
    {
        return [
            'type' => $this->getMethodReturnType(),
            // 'column' => $this->getRelationColumn(),
            'method_name' => $this->getName(),
        ];
    }

    protected function getRelationClass()
    {
    }

    protected function getMethodReturnType()
    {
        if ($this->hasReturnType()) {
            return $this->getReturnType()->getName();
        }
    }

    protected function getRelationColumn()
    {
    }

    /**
     * Dynamically pass method calls to the underlying resource.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->resource, $method, $parameters);
    }
}
