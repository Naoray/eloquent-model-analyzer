<?php

namespace Naoray\EloquentModelAnalyzer\Detectors;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Doctrine\DBAL\Schema\Index;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Naoray\EloquentModelAnalyzer\Contracts\Detector;
use Naoray\EloquentModelAnalyzer\Traits\InteractsWithRelationMethods;

class FieldsDetector implements Detector
{
    use InteractsWithRelationMethods;

    public function analyze(Model $resource): array
    {
        // get table name from model
        $tableName = $resource->getTable();
        $tableIndexes = DB::getDoctrineSchemaManager()->listTableIndexes($tableName);

        // get column names
        $columnListing = Schema::getColumnListing($tableName);

        return collect($columnListing)->mapWithKeys(function ($column) use ($tableName, $tableIndexes) {
            $data = (object)DB::connection()->getDoctrineColumn($tableName, $column)->toArray();

            $indexes = Arr::where($tableIndexes, function (Index $index) use ($column) {
                return in_array($column, $index->getColumns());
            });

            return [
                $column => [
                    'name' => $column,
                    'type' => get_class($data->type),
                    'unsigned' => $data->unsigned,
                    'unique' => (bool)Arr::first($indexes, function (Index $index) {
                        return $index->isUnique();
                    }),
                    'isForeignKey' => (bool)Arr::where(array_keys($indexes), function ($key) use ($column) {
                        return Str::contains($key, '_foreign') && Str::contains($key, '_' . $column . '_');
                    }),
                    'nullable' => !$data->notnull,
                    'autoincrement' => $data->autoincrement,
                ],
            ];
        })->all();
    }
}
