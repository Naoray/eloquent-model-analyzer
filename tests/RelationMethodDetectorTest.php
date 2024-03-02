<?php

namespace Naoray\EloquentModelAnalyzer\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Naoray\EloquentModelAnalyzer\Detectors\RelationMethodDetector;

class RelationMethodDetectorTest extends TestCase
{
    /** @test */
    public function it_can_get_relation_methods_of_a_model_by_return_type()
    {
        $relationMethods = (new RelationMethodDetector(UserWithReturnTypes::class))->discover();

        $this->assertCount(2, $relationMethods);
        $this->assertEquals([
            'relatedClass' => UserWithReturnTypes::class,
            'type'         => BelongsTo::class,
            'foreignKey'   => 'parent_id',
            'ownerKey'     => 'id',
            'methodName'   => 'parent',
        ], $relationMethods->first()->toArray());
        $this->assertEquals([
            'relatedClass' => Post::class,
            'type'         => HasMany::class,
            'foreignKey'   => 'user_id',
            'ownerKey'     => 'id',
            'methodName'   => 'posts',
        ], $relationMethods->get(1)->toArray());
    }

    /** @test */
    public function it_can_get_relation_methods_of_a_model_by_doc_comment()
    {
        $user = new UserWithDocComments();

        $relationMethods = (new RelationMethodDetector($user))->discover();

        $this->assertCount(2, $relationMethods);
        $this->assertEquals([
            'relatedClass' => UserWithDocComments::class,
            'type'         => BelongsTo::class,
            'foreignKey'   => 'parent_id',
            'ownerKey'     => 'id',
            'methodName'   => 'parent',
        ], $relationMethods->first()->toArray());
        $this->assertEquals([
            'relatedClass' => Post::class,
            'type'         => HasMany::class,
            'foreignKey'   => 'user_id',
            'ownerKey'     => 'id',
            'methodName'   => 'posts',
        ], $relationMethods->get(1)->toArray());
    }

    /** @test */
    public function it_can_get_relation_methods_of_a_model_by_method_content()
    {
        $user = new UserWithoutAnyHints();

        $relationMethods = (new RelationMethodDetector($user))->discover();

        $this->assertCount(2, $relationMethods);
        $this->assertEquals([
            'relatedClass' => UserWithoutAnyHints::class,
            'type'         => BelongsTo::class,
            'foreignKey'   => 'parent_id',
            'ownerKey'     => 'id',
            'methodName'   => 'parent',
        ], $relationMethods->first()->toArray());
        $this->assertEquals([
            'relatedClass' => Post::class,
            'type'         => HasMany::class,
            'foreignKey'   => 'user_id',
            'ownerKey'     => 'id',
            'methodName'   => 'posts',
        ], $relationMethods->get(1)->toArray());
    }

    /** @test */
    public function it_can_detect_many_to_many_relation_methods()
    {
        $book = new Book();

        $relationMethods = (new RelationMethodDetector($book))->discover();

        $this->assertCount(1, $relationMethods);
        $this->assertEquals([
            'relatedClass' => Author::class,
            'type'         => BelongsToMany::class,
            'foreignKey'   => 'book_id',
            'ownerKey'     => 'author_id',
            'methodName'   => 'authors',
        ], $relationMethods->first()->toArray());
    }

    /** @test */
    public function it_can_handle_multiple_return_types()
    {
        $author = new Author();

        $relationMethods = (new RelationMethodDetector($author))->discover();
        $this->assertCount(1, $relationMethods);
        $this->assertEquals([
            'relatedClass' => Book::class,
            'type'         => BelongsToMany::class,
            'foreignKey'   => 'author_id',
            'ownerKey'     => 'book_id',
            'methodName'   => 'books',
        ], $relationMethods->first()->toArray());
    }
}

class UserWithReturnTypes extends Model
{
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id');
    }
}

class UserWithDocComments extends Model
{
    /**
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(self::class);
    }

    /**
     * @return HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }
}

class UserWithoutAnyHints extends Model
{
    public function parent()
    {
        return $this->belongsTo(self::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }
}

class Post extends Model
{
}

class Book extends Model
{
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }
}

class Author extends Model
{
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }

    public function stringOrInt(bool $returnInt): int|string
    {
        return $returnInt ? 1 : '1';
    }
}
