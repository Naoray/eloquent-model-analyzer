<?php

namespace Naoray\EloquentModelAnalyzer\Detectors;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
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

        // MySQL 5.7 backward compability
        $databasePlatform = DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform();
        if (get_class($databasePlatform) === 'Doctrine\DBAL\Platforms\MySQL57Platform') {
            $databasePlatform->registerDoctrineTypeMapping('enum', 'string');
        }
    }

    public function discover(): Collection
    {
        $tableName = $this->model->getTable();

        return collect(Schema::getColumnListing($tableName))
            ->mapWithKeys(function ($column) use ($tableName) {
                return [$column => new Column($column, $tableName)];
            });
    }
}
