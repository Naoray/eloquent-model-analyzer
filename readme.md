# eloquent-model-analyzer

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/naoray/eloquent-model-analyzer.svg?style=flat-square)](https://packagist.org/packages/naoray/eloquent-model-analyzer)
![Tests](https://github.com/naoray/eloquent-model-analyzer/workflows/Run%20Tests%20-%20Current/badge.svg?branch=master)

Analyzing an Eloquent Model for its relations and columns can be overwhelming. This little library aims to make it as simple as possible.

You probably wonder why you would ever need to analyze your Models at runtime?! All scenarios I can think of are related to analyzing the codebase to generate some code bits. Here are some scenarios where this might come in handy:
- automatically create factories for your models as shown in [laravel-prefill-factory](https://github.com/naoray/laravel-factory-prefill) or [factory-generator](https://github.com/laravel-shift/factory-generator)
- it could be use to create something like the `trace` command in [laravel-shift/blueprint](https://github.com/laravel-shift/blueprint)

## Install
`composer require naoray/eloquent-model-analyzer`

## Usage
### Getting all relations of a Model
There are three different strategies for getting all relation methods of an Eloquent Model:
- checking the return types of the methods
- extracting the return types from the doc method
- call the method directly and check the instance of what is returned

```php
// User.php
class User extends Model
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

// get relations
// type of $columns is \Illuminate\Support\Collection
$relations = Analyzer::relations(User::class);

// get the first relation
$relation = $relations->first();

// all relations implement the Arrayable interface
$relation->toArray();
// [
//     'relatedClass' => User::class,
//     'type' => \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
//     'foreignKey' => 'parent_id',
//     'ownerKey' => 'id',
//     'methodName' => 'parent',
// ]
```

The `RelationMethod` Class forwards all method calls which aren't present on the class directly to the underlying `ReflectionMethod` class.

### Getting all Columns of a Model
```php
// CreateUserTable.php
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->json('bio')->nullable();
    });
}

// get columns
// type of $columns is \Illuminate\Support\Collection
$columns = Analyzer::columns(User::class);

// get a single column by column name
$column = $columns->get('name');

// all columns implement the Arrayable interface
$column->toArray();
// [
//     'name' => 'name',
//     'type' => \Doctrine\DBAL\Types\StringType::class,
//     'unsigned' => false,
//     'unique' => false,
//     'isForeignKey' => false,
//     'nullable' => false,
//     'autoincrement' => false,
// ]
```

The `Column` class forwards all method calls which aren't present on the class directly to the underlying `DBAL\Schema\Column` Class.

## Testing
Run the tests with:

``` bash
vendor/bin/phpunit
```

## Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security
If you discover any security-related issues, please email krishan.koenig@googlemail.com instead of using the issue tracker.

## License
The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.
