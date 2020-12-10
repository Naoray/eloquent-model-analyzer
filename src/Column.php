<?php

namespace Naoray\EloquentModelAnalyzer;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Doctrine\DBAL\Schema\Index;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\ForwardsCalls;
use Doctrine\DBAL\Schema\Column as DbalColumn;

class Column implements Arrayable
{
    use ForwardsCalls;

    /**
     * @var string
     */
    protected $column;

    /**
     * @var DbalColumn
     */
    protected $data;

    /**
     * @var string
     */
    protected $table;

    /**
     * @param string $column
     * @param string $table
     */
    public function __construct(string $column, string $table)
    {
        $this->table = $table;
        $this->column = $column;
        $this->data = DB::connection()->getDoctrineColumn($table, $column);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->column,
            'type' => $this->typeClass(),
            'unsigned' => $this->data->getUnsigned(),
            'unique' => $this->isUnique(),
            'isForeignKey' => $this->isForeignKey(),
            'nullable' => !$this->data->getNotnull(),
            'autoincrement' => $this->data->getAutoincrement(),
        ];
    }

    public function typeClass(): string
    {
        return get_class($this->data->getType());
    }

    public function isUnique(): bool
    {
        return (bool)Arr::first($this->indexes(), function (Index $index) {
            return $index->isUnique();
        });
    }

    public function isForeignKey(): bool
    {
        return (bool)Arr::where(array_keys($this->indexes()), function ($key) {
            return Str::contains($key, '_foreign') && Str::contains($key, '_' . $this->column . '_');
        });
    }

    public function indexes(): array
    {
        $allIndexes = DB::getDoctrineSchemaManager()->listTableIndexes($this->table);

        return Arr::where($allIndexes, function (Index $index) {
            return in_array($this->column, $index->getColumns());
        });
    }

    /**
     * Dynamically pass method calls to the underlying column.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->data, $method, $parameters);
    }
}
