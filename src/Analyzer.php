<?php

namespace Naoray\EloquentModelAnalyzer;

use Illuminate\Database\Eloquent\Model;
use Naoray\EloquentModelAnalyzer\Detectors\RelationMethodDetector;

class Analyzer
{
    public function relationMethodsOf(Model $model)
    {
        return (new RelationMethodDetector)->analyze($model);
    }
}
