<?php

namespace Naoray\EloquentModelAnalyzer\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionMethod;
use ReflectionType;
use SplFileObject;

trait InteractsWithRelationMethods
{
    /**
     * Get all relation types a model can have.
     */
    protected static function getRelationTypes(): array
    {
        return [
            BelongsTo::class,
            BelongsToMany::class,
            HasMany::class,
            HasManyThrough::class,
            HasOne::class,
            HasOneThrough::class,
            MorphOne::class,
            MorphTo::class,
            MorphToMany::class,
        ];
    }

    /**
     * @param ReflectionType $type
     * @return bool
     */
    protected function isRelationReturnType(ReflectionType $type): bool
    {
        return in_array($type->getName(), $this->getRelationTypes());
    }

    /**
     * @param ReflectionMethod $method
     * @return bool
     */
    protected function hasReturnTypeInDoc(ReflectionMethod $method): bool
    {
        return Str::contains($method->getDocComment(), '@return');
    }

    /**
     * @param ReflectionMethod $method
     * @return bool
     */
    protected function hasRelationTypeInDoc(ReflectionMethod $method): bool
    {
        return (bool) $this->extractReturnTypeFromDocs(
            $method->getDocComment()
        );
    }

    /**
     * @param string $docComment
     * @return string
     */
    protected function extractReturnTypeFromDocs(string $docComment)
    {
        return Arr::first(static::getRelationTypes(), function ($type) use ($docComment) {
            return Str::contains($docComment, '@return '.class_basename($type))
                || Str::contains($docComment, "@return \\$type");
        });
    }

    /**
     * Get relation method written code.
     *
     * @return string
     */
    protected function getRelationMethodContent(ReflectionMethod $method)
    {
        $file = new SplFileObject($method->getFileName());
        $file->seek($method->getStartLine() - 1);

        $code = '';
        while ($file->key() < $method->getEndLine()) {
            $code .= $file->current();
            $file->next();
        }

        return Str::of($code)
            ->replaceMatches('/\s\s+/', '')
            ->after('function')
            ->before('}')
            ->trim();
    }
}
