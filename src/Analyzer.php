<?php

namespace Naoray\EloquentModelAnalyzer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Naoray\EloquentModelAnalyzer\Detectors\ColumnsDetector;
use Naoray\EloquentModelAnalyzer\Detectors\RelationMethodDetector;

class Analyzer
{
    public static function relations(Model $model): Collection
    {
        return (new RelationMethodDetector($model))->analyze();
    }

    public static function columns(Model $model): Collection
    {
        return (new ColumnsDetector($model))->analyze();
    }
}
