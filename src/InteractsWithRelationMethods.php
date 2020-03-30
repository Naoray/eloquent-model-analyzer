<?php

namespace Naoray\EloquentModelAnalyzer;

use ReflectionMethod;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

trait InteractsWithRelationMethods
{
    protected static function getRelationTypes()
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

    protected function getReturnTypeFromDoc($docComment)
    {
        return Arr::first(static::getRelationTypes(), function ($type) use ($docComment) {
            return Str::contains($docComment, '@return ' . class_basename($type))
                || Str::contains($docComment, "@return $type");
        });
    }

    /**
     * Get relation method written code.
     *
     * @return string
     */
    protected function getRelationMethodContent(ReflectionMethod $method)
    {
        $file = new \SplFileObject($method->getFileName());
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
