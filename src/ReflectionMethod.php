<?php

namespace Naoray\EloquentModelAnalyzer;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ReflectionMethod
{
    use ForwardsCalls;

    protected $resource;

    protected static $relationTypes = [
        BelongsTo::class,
        BelongsToMany::class,
        HasMany::class,
        HasManyThrough::class,
        HasOne::class,
        HasOneThrough::class,
        MorphOne::class,
        MorphTo::class,
        MorphToMany::class,
    ];

    public function __construct(\ReflectionMethod $method)
    {
        $this->resource = $method;
    }

    public function isRelationMethod()
    {
        return $this->methodIsNotFromBaseClass()
            && (
                $this->filterByReturnType()
                || $this->filterByDocComment()
            );
        // || $this->filterByContent($method);
    }

    protected function methodIsNotFromBaseClass()
    {
        return !method_exists(Model::class, $this->getName());
    }

    protected function filterByReturnType(): bool
    {
        if (!$this->hasReturnType()) {
            return false;
        }

        $returnType = $this->getReturnType();

        return in_array($returnType->getName(), static::$relationTypes);
    }

    protected function filterByDocComment(): bool
    {
        if (!$docComment = $this->getDocComment()) {
            return false;
        }

        return Str::contains($docComment, static::$relationTypes);
    }

    protected function filterByContent()
    {
        /*
         * @todo
         */
    }

    public function toArray()
    {
        $key = $this->getRelationClass();

        return [
            $key => [
                'type' => $this->getMethodReturnType(),
                // 'column' => $this->getRelationColumn(),
                'method_name' => $this->getName(),
            ],
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
