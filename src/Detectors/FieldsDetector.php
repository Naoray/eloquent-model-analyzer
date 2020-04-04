<?php

namespace Naoray\EloquentModelAnalyzer\Detectors;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Naoray\EloquentModelAnalyzer\Field;
use Naoray\EloquentModelAnalyzer\Contracts\Detector;
use Naoray\EloquentModelAnalyzer\Traits\InteractsWithRelationMethods;

class FieldsDetector implements Detector
{
    use InteractsWithRelationMethods;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function analyze(): Collection
    {
        $tableName = $this->model->getTable();

        return collect(Schema::getColumnListing($tableName))
            ->mapWithKeys(function ($column) use ($tableName) {
                return [$column => new Field($column, $tableName)];
            });
    }
}
