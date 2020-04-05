<?php

namespace Naoray\EloquentModelAnalyzer\Detectors;

use ReflectionMethod;
use ReflectionObject;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Naoray\EloquentModelAnalyzer\RelationMethod;
use Illuminate\Database\Eloquent\Relations\Relation;
use Naoray\EloquentModelAnalyzer\Contracts\Detector;
use Naoray\EloquentModelAnalyzer\Traits\InteractsWithRelationMethods;

class RelationMethodDetector implements Detector
{
    use InteractsWithRelationMethods;

    /**
     * @var Model|string
     */
    protected $model;

    /**
     * @var ReflectionObject
     */
    protected $reflection;

    /**
     * @param Model|string $model
     */
    public function __construct($model)
    {
        $this->model = is_string($model) ? new $model : $model;
        $this->reflection = new ReflectionObject($this->model);
    }

    /**
     * Analyzes given model with reflection to gain all methods which return
     * Relation instances e.g. belongsTo, hasMany, hasOne, etc.
     *
     * @return Collection
     */
    public function analyze(): Collection
    {
        return collect($this->reflection->getMethods(ReflectionMethod::IS_PUBLIC))
            ->filter(function (ReflectionMethod $method) {
                return $this->isRelationMethod($method);
            })
            ->map(function ($method) {
                return new RelationMethod($method, $this->model, $this->reflection);
            });
    }

    protected function isRelationMethod(ReflectionMethod $method): bool
    {
        if (method_exists(Model::class, $method->getName())) {
            return false;
        }

        if ($method->hasReturnType()) {
            return $this->isRelationReturnType($method->getReturnType());
        }

        if ($method->getDocComment() && $this->hasReturnTypeInDoc($method)) {
            return $this->hasRelationTypeInDoc($method);
        }

        if ($method->getNumberOfParameters() > 0) {
            return false;
        }

        $relationObject = $this->model->{$method->getName()}();

        return $relationObject instanceof Relation;
    }
}
