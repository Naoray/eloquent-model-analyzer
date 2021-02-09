<?php

namespace Naoray\EloquentModelAnalyzer;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use Naoray\EloquentModelAnalyzer\Traits\InteractsWithRelationMethods;
use ReflectionClass;
use ReflectionMethod;

class RelationMethod implements Arrayable
{
    use ForwardsCalls;
    use InteractsWithRelationMethods;

    /**
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var ReflectionMethod
     */
    protected $method;

    /**
     * @param ReflectionMethod $method
     * @param Model            $model
     * @param ReflectionClass  $reflection
     */
    public function __construct(ReflectionMethod $method, Model $model, ReflectionClass $reflection)
    {
        $this->method = $method;
        $this->model = $model;
        $this->reflection = $reflection;
    }

    public function toArray(): array
    {
        return [
            'relatedClass' => $this->getRelatedClass(),
            'type' => $this->returnType(),
            'foreignKey' => $this->foreignKey(),
            'ownerKey' => $this->ownerKey(),
            'methodName' => $this->getName(),
        ];
    }

    public function getRelatedClass(): string
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
        if (! $className->is('*::class')) {
            return $className;
        }

        $className = $className->before('::class')
            ->__toString();

        return $className === 'self' || $className === class_basename($this->reflection->getName())
            ? $this->reflection->getName()
            : $this->reflection->getNamespaceName().'\\'.$className;
    }

    public function returnType(): string
    {
        if ($this->hasReturnType()) {
            return $this->getReturnType()->getName();
        }

        if ($this->hasReturnTypeInDoc($this->method)) {
            return $this->extractReturnTypeFromDocs($this->getDocComment());
        }

        return get_class($this->getRelation());
    }

    public function foreignKey(): string
    {
        $relationObj = $this->getRelation();

        return method_exists($relationObj, 'getForeignKeyName')
            ? $relationObj->getForeignKeyName()
            : $relationObj->getForeignKey();
    }

    public function ownerKey(): string
    {
        $relationObj = $this->getRelation();

        return method_exists($relationObj, 'getOwnerKeyName')
            ? $relationObj->getOwnerKeyName()
            : $relationObj->getLocalKeyName();
    }

    public function getRelation(): Relation
    {
        return $this->model->{$this->getName()}();
    }

    /**
     * Dynamically pass method calls to the underlying method.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->method, $method, $parameters);
    }
}
