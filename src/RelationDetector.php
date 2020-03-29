<?php

namespace Naoray\EloquentModelAnalyzer;

use ReflectionClass;

class RelationDetector
{
    public function __construct($resource) {
        $this->resource = $resource;
    }
}
