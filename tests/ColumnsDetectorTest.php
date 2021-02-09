<?php

namespace Naoray\EloquentModelAnalyzer\Tests;

use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Naoray\EloquentModelAnalyzer\Detectors\ColumnsDetector;

class ColumnsDetectorTest extends TestCase
{
    /** @test */
    public function it_can_detect_all_fields_from_the_database_table()
    {
        $userMigration = new class() extends Migration {
            public function up()
            {
                Schema::create('users', function (Blueprint $table) {
                    $table->id();
                    $table->enum('name', ['test']);
                    $table->string('email')->unique();
                    $table->json('bio')->nullable();
                });
            }
        };
        $userMigration->up();

        $fields = (new ColumnsDetector(User::class))->discover();

        $this->assertCount(4, $fields);
        $this->assertEquals([
            'name'          => 'id',
            'type'          => IntegerType::class,
            'unsigned'      => false,
            'unique'        => true,
            'isForeignKey'  => false,
            'nullable'      => false,
            'autoincrement' => true,
        ], $fields->get('id')->toArray());
        $this->assertEquals([
            'name'          => 'name',
            'type'          => StringType::class,
            'unsigned'      => false,
            'unique'        => false,
            'isForeignKey'  => false,
            'nullable'      => false,
            'autoincrement' => false,
        ], $fields->get('name')->toArray());
        $this->assertEquals([
            'name'          => 'email',
            'type'          => StringType::class,
            'unsigned'      => false,
            'unique'        => true,
            'isForeignKey'  => false,
            'nullable'      => false,
            'autoincrement' => false,
        ], $fields->get('email')->toArray());
        $this->assertEquals([
            'name'          => 'bio',
            'type'          => TextType::class,
            'unsigned'      => false,
            'unique'        => false,
            'isForeignKey'  => false,
            'nullable'      => true,
            'autoincrement' => false,
        ], $fields->get('bio')->toArray());
    }
}

class User extends Model
{
}
