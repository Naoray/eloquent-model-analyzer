<?php

namespace Naoray\EloquentModelAnalyzer\Detectors;

use ReflectionMethod;
use ReflectionObject;
use Illuminate\Database\Eloquent\Model;
use Naoray\EloquentModelAnalyzer\RelationMethod;
use Illuminate\Database\Eloquent\Relations\Relation;
use Naoray\EloquentModelAnalyzer\Contracts\Detector;
use Naoray\EloquentModelAnalyzer\Traits\InteractsWithRelationMethods;

class FieldsDetector implements Detector
{
    use InteractsWithRelationMethods;

    public function analyze(Model $resource): array
    {
        $reflectionObject = new ReflectionObject($resource);

        return collect($reflectionObject->getMethods(ReflectionMethod::IS_PUBLIC))
            ->filter(function (ReflectionMethod $method) use ($resource) {
                return $this->methodIsNotFromBaseClass($method)
                        && (
                            $this->filterByReturnType($method)
                            || $this->filterByDocComment($method)
                            || $this->filterByContent($method, $resource)
                        );
            })
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

        return in_array($returnType->getName(), $this->getRelationTypes());
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
