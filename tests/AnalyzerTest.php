<?php

namespace Naoray\EloquentModelAnalyzer\Tests;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Model;
use Naoray\EloquentModelAnalyzer\Analyzer;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyzerTest extends TestCase
{
    /** @test */
    public function it_can_get_relation_methods_of_a_model_by_return_type()
    {
        $user = new UserWithReturnTypes();

        $relationMethods = (new Analyzer())
            ->relationsOf($user);

        var_dump($relationMethods);
        $this->assertCount(2, $relationMethods);
        $this->assertEquals([
            UserWithReturnTypes::class => [
                'type' => BelongsTo::class,
                // 'column' => 'parent_id',
                'method_name' => 'parent',
            ],
            Post::class => [
                'type' => HasMany::class,
                // 'column' => 'id',
                'method_name' => 'posts',
            ],
        ], $relationMethods);
    }

    /** @test */
    public function it_can_get_relation_methods_of_a_model_by_doc_comment()
    {
    }

    /** @test */
    public function it_can_get_relation_methods_of_a_model_by_method_content()
    {
    }

    /** @test */
    public function it_can_get_all_()
    {
        //test it
    }
}

class UserWithReturnTypes extends Model
{
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function posts(): HasMany
    {
        return $this->HasMany(Post::class);
    }
}

class Post extends Model
{
}
