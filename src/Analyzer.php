<?php

namespace Naoray\EloquentModelAnalyzer;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Naoray\EloquentModelAnalyzer\Detectors\FieldsDetector;
use Naoray\EloquentModelAnalyzer\Detectors\RelationMethodDetector;

class Analyzer
{
    public static function relations(Model $model): Collection
    {
        return (new RelationMethodDetector($model))->analyze();
    }

    public static function fields(Model $model): Collection
    {
        return (new FieldsDetector($model))->analyze();
    }
}
