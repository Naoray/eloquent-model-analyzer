<?php

namespace Naoray\EloquentModelAnalyzer\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Naoray\EloquentModelAnalyzer\EloquentModelAnalyzerServiceProvider::class,
        ];
    }
}
