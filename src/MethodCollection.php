<?php

namespace Naoray\EloquentModelAnalyzer;

use ReflectionMethod;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class MethodCollection extends Collection
{
    public function filterRelationMethods($resource)
    {
        return $this->filter(function (ReflectionMethod $method) use ($resource) {
            return $this->methodIsNotFromBaseClass($method)
                    && (
                        $this->filterByReturnType($method)
                        || $this->filterByDocComment($method)
                        || $this->filterByContent($method, $resource)
                    );
        });
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
