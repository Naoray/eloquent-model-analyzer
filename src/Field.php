<?php

namespace Naoray\EloquentModelAnalyzer;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Column;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\ForwardsCalls;

class Field implements Arrayable
{
    use ForwardsCalls;

    /**
     * @var string
     */
    protected $column;

    /**
     * @var Column
     */
    protected $data;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $indexes;

    /**
     * @param string $column
     * @param string $table
     * @param array $indexes
     */
    public function __construct(string $column, string $table, array $indexes)
    {
        $this->table = $table;
        $this->column = $column;
        $this->indexes = $indexes;
        $this->data = DB::connection()->getDoctrineColumn($table, $column);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->column,
            'type' => $this->typeClass(),
            'unsigned' => $this->data->getUnsigned(),
            'unique' => $this->isUnique(),
            'isForeignKey' => (bool)Arr::where(array_keys($this->indexes()), function ($key) {
                return Str::contains($key, '_foreign') && Str::contains($key, '_' . $this->column . '_');
            }),
            'nullable' => !$this->data->notnull,
            'autoincrement' => $this->data->autoincrement,
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

    public function indexes()
    {
        return Arr::where($this->indexes, function (Index $index) {
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
