<?php

namespace Naoray\EloquentModelAnalyzer;

use ReflectionClass;
use ReflectionObject;
use Illuminate\Database\Eloquent\Model;

class Analyzer
{
    public function relationsOf(Model $resource)
    {
        return (new RelationDetector)
            ->analyze($resource);

        // return collect($reflection->getMethods())
        //     ->mapInto(ReflectionMethod::class)
        //     ->filter->isRelationMethod()
        //     ->mapWithKeys(function ($method) use ($resource) {
        //         return $method->toArray($resource);
        //     })
        //     ->all();
    }

    protected function reflect($resource)
    {
        return is_object($resource)
            ? new ReflectionObject($resource)
            : new ReflectionClass($resource);
    }
}
