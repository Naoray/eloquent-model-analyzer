<?php

namespace Naoray\EloquentModelAnalyzer;

use Illuminate\Support\ServiceProvider;
use Naoray\EloquentModelAnalyzer\Detectors\RelationMethodDetector;

class EloquentModelAnalyzerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Analyzer::class, function ($app) {
            return new Analyzer([
                'relationsMethods' => RelationMethodDetector::class,
            ]);
        });
    }
}
