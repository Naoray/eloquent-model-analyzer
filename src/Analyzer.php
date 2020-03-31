<?php

namespace Naoray\EloquentModelAnalyzer;

class Analyzer
{
    /**
     * @var array
     */
    protected $detectors;

    public function __construct(array $detectors)
    {
        $this->detectors = $detectors;
    }

    public function __call($method, $args)
    {
        if (!array_key_exists($method, $this->detectors)) {
            return;
        }

        return $this->resolveDetector($method)->analyze(...$args);
    }

    protected function resolveDetector($name)
    {
        return new $this->detectors[$name];
    }
}
