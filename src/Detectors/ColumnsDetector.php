<?php

namespace Naoray\EloquentModelAnalyzer\Detectors;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Naoray\EloquentModelAnalyzer\Column;
use Naoray\EloquentModelAnalyzer\Contracts\Detector;
use Naoray\EloquentModelAnalyzer\Traits\InteractsWithRelationMethods;

class ColumnsDetector implements Detector
{
    use InteractsWithRelationMethods;

    /**
     * @var Model|string
     */
    protected $model;

    /**
     * @param Model|string $model
     */
    public function __construct($model)
    {
        $this->model = is_string($model) ? new $model : $model;
    }

    public function analyze(): Collection
    {
        $tableName = $this->model->getTable();

        return collect(Schema::getColumnListing($tableName))
            ->mapWithKeys(function ($column) use ($tableName) {
                return [$column => new Column($column, $tableName)];
            });
    }
}
