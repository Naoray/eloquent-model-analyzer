# Changelog

## [v3.0.0](https://github.com/Naoray/eloquent-model-analyzer/tree/v3.0.0) (2022-02-08)

**Added**
- support for Laravel 9 (#23)

[Full Changelog](https://github.com/naoray/eloquent-model-analyzer/compare/v2.1.3..v3.0.0)

## [v2.1.3](https://github.com/Naoray/eloquent-model-analyzer/tree/v2.1.3) (2021-08-09)

**Fixes**
- Only invoke relation methods https://github.com/Naoray/eloquent-model-analyzer/pull/22/commits/5a79919b073afec7940d8f9d75a1eb6e30466a2f - thanks to @mortenscheel

[Full Changelog](https://github.com/naoray/eloquent-model-analyzer/compare/v2.1.2..v2.1.3)

## [v2.1.2](https://github.com/Naoray/eloquent-model-analyzer/tree/v2.1.2) (2021-05-31)

**Fixes**
- allow `doctrine/dbal:^3.0` to solve version conflicts for package users

[Full Changelog](https://github.com/naoray/eloquent-model-analyzer/compare/v2.1.1..v2.1.2)

## [v2.1.1](https://github.com/Naoray/eloquent-model-analyzer/tree/v2.1.1) (2021-02-09)

**Fixed**
- security dependency updates

[Full Changelog](https://github.com/naoray/eloquent-model-analyzer/compare/v2.1.0..v2.1.1)

## [v2.1.0](https://github.com/Naoray/eloquent-model-analyzer/tree/v2.1.0) (2020-12-10)

**Added**
- support for MySQL 5.7 enum detections

[Full Changelog](https://github.com/naoray/eloquent-model-analyzer/compare/v2.0.1..v2.1.0)

## [v2.0.1](https://github.com/Naoray/eloquent-model-analyzer/tree/v2.0.1) (2020-09-24)

**Fixed**
- removed discovering not used service provider

[Full Changelog](https://github.com/naoray/eloquent-model-analyzer/compare/v2.0.0..v2.0.1)

## [v2.0.0](https://github.com/Naoray/eloquent-model-analyzer/tree/v2.0.0) (2020-09-23)

**Added**
- support for Laravel 8 (b328fefb987ee87ae04aea501d08671b5c3b5b06)

**Changed**
- renamed `Naoray\EloquentModelAnalyzer\Contracts\Detector`'s `analyze()` method into `discover()`

[Full Changelog](https://github.com/naoray/eloquent-model-analyzer/compare/v1.0.2..v2.0.0)

## [v1.0.2](https://github.com/Naoray/eloquent-model-analyzer/tree/v1.0.2) (2020-04-05)

**Fixed**
- removed use of `Str::of()` since it is not compatible with Laravel v6 usage

[Full Changelog](https://github.com/naoray/eloquent-model-analyzer/compare/v1.0.1..v1.0.2)

## [v1.0.1](https://github.com/Naoray/eloquent-model-analyzer/tree/v1.0.1) (2020-04-05)

**Initial Release**
- `relations()` method to retrieve all relation methods of a model
- `columns()` method to retrieve all columns of a model
