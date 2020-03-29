<?php

namespace Naoray\EloquentModelAnalyzer;

use ReflectionMethod;
use ReflectionObject;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class RelationDetector
{
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

    public function analyze($resource)
    {
        $this->resource = $resource;
        $reflectionObject = new ReflectionObject($resource);

        $relations = collect($reflectionObject->getMethods(ReflectionMethod::IS_PUBLIC))
            ->filter(function ($method) {
                return $this->methodIsNotFromBaseClass($method)
                    && (
                        $this->filterByReturnType($method)
                        || $this->filterByDocComment($method)
                    );
            })->mapInto(RelationMethod::class)
            ->mapWithKeys(function ($method) {
                return $method->toArray();
            })->all();

        return [
            get_class($this->resource) => $relations,
        ];
    }

    protected function methodIsNotFromBaseClass(ReflectionMethod $method)
    {
        return !method_exists(Model::class, $method->getName());
    }

    protected function filterByReturnType(ReflectionMethod $method): bool
    {
        if (!$method->hasReturnType()) {
            return false;
        }

        $returnType = $method->getReturnType();

        return in_array($returnType->getName(), static::$relationTypes);
    }

    protected function filterByDocComment(ReflectionMethod $method): bool
    {
        if (!$docComment = $method->getDocComment()) {
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
}
