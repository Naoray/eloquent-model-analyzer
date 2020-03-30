<?php

namespace Naoray\EloquentModelAnalyzer;

use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\ForwardsCalls;

class RelationMethod implements Arrayable
{
    use ForwardsCalls, InteractsWithRelationMethods;

    protected $reflection;

    public function __construct(array $data)
    {
        $this->method = $data['method'];
        $this->model = $data['model'];
        $this->reflection = $data['reflection'];
    }

    protected function getRelatedClass()
    {
        $methodContent = $this->getRelationMethodContent($this->method);

        /**
         * 'author(): BelongsTo {return $this->belongsTo(User::class, 'author_id');'.
         */
        $className = Str::of($methodContent)
            ->after('{return')
            ->after('(')
            ->before(',')
            ->trim("(');");

        /*
         * If classname does not use ::class notation
         * we consider it as a full class string reference.
         */
        if (!$className->is('*::class')) {
            return $className;
        }

        $className = $className->before('::class')
            ->__toString();

        return $className === 'self' || $className === class_basename($this->reflection->getName())
            ? $this->reflection->getName()
            : $this->reflection->getNamespaceName() . '\\' . $className;
    }

    public function toArray()
    {
        return [
            $this->getRelatedClass() => [
                'type' => $this->getMethodReturnType(),
                'foreignKey' => $this->getForeignKeyName(),
                'ownerKey' => $this->getOwnerKeyName(),
                'methodName' => $this->getName(),
            ],
        ];
    }

    protected function getMethodReturnType()
    {
        if ($this->hasReturnType()) {
            return $this->getReturnType()->getName();
        }

        if ($docComment = $this->getDocComment()) {
            return $this->getReturnTypeFromDoc($docComment);
        }

        return get_class($this->getRelation());
    }

    protected function getForeignKeyName()
    {
        $relationObj = $this->getRelation();

        return method_exists($relationObj, 'getForeignKeyName')
            ? $relationObj->getForeignKeyName()
            : $relationObj->getForeignKey();
    }

    protected function getOwnerKeyName()
    {
        $relationObj = $this->getRelation();

        return method_exists($relationObj, 'getOwnerKeyName')
            ? $relationObj->getOwnerKeyName()
            : $relationObj->getLocalKeyName();
    }

    protected function getRelation()
    {
        return $this->model->{$this->getName()}();
    }

    /**
     * Dynamically pass method calls to the underlying method.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->method, $method, $parameters);
    }
}
