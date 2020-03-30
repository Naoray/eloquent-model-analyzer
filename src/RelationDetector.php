<?php

namespace Naoray\EloquentModelAnalyzer;

use ReflectionMethod;
use ReflectionObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class RelationDetector
{
    use InteractsWithRelationMethods;

    public function analyze($resource)
    {
        $reflectionObject = new ReflectionObject($resource);

        return collect($reflectionObject->getMethods(ReflectionMethod::IS_PUBLIC))
            ->filterRelationMethods($resource)
            ->map(function ($method) use ($resource, $reflectionObject) {
                return [
                    'method' => $method,
                    'model' => $resource,
                    'reflection' => $reflectionObject,
                ];
            })
            ->mapInto(RelationMethod::class)
            ->mapwithKeys(function ($method) {
                return $method->toArray();
            })
            ->all();
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

        return in_array($returnType->getName(), static::getRelationTypes());
    }

    protected function filterByDocComment(ReflectionMethod $method): bool
    {
        if (!$docComment = $method->getDocComment()) {
            return false;
        }

        return (bool)$this->getReturnTypeFromDoc($docComment);
    }

    protected function filterByContent(ReflectionMethod $method, Model $resource)
    {
        if ($method->getNumberOfParameters() > 0) {
            return false;
        }

        $relationObject = $resource->{$method->getName()}();

        return $relationObject instanceof Relation;
    }
}
