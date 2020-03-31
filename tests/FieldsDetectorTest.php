<?php

namespace Naoray\EloquentModelAnalyzer\Tests;

use Doctrine\DBAL\Types\TextType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\IntegerType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Naoray\EloquentModelAnalyzer\Detectors\FieldsDetector;

class FieldsDetectorTest extends TestCase
{
    /** @test */
    public function it_can_detect_all_fields_from_the_database_table()
    {
        $userMigration = new class extends Migration {
            public function up()
            {
                Schema::create('users', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('email')->unique();
                    $table->json('bio')->nullable();
                });
            }
        };
        $userMigration->up();
        $user = new User();

        $fields = (new FieldsDetector())->analyze($user);

        $this->assertCount(4, $fields);
        $this->assertEquals([
            'id' => [
                'name' => 'id',
                'type' => IntegerType::class,
                'unsigned' => false,
                'unique' => true,
                'isForeignKey' => false,
                'nullable' => false,
                'autoincrement' => true,
            ],
            'name' => [
                'name' => 'name',
                'type' => StringType::class,
                'unsigned' => false,
                'unique' => false,
                'isForeignKey' => false,
                'nullable' => false,
                'autoincrement' => false,
            ],
            'email' => [
                'name' => 'email',
                'type' => StringType::class,
                'unsigned' => false,
                'unique' => true,
                'isForeignKey' => false,
                'nullable' => false,
                'autoincrement' => false,
            ],
            'bio' => [
                'name' => 'bio',
                'type' => TextType::class,
                'unsigned' => false,
                'unique' => false,
                'isForeignKey' => false,
                'nullable' => true,
                'autoincrement' => false,
            ],
        ], $fields);
    }
}

class User extends Model
{
}
