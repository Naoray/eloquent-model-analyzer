<?php

namespace Naoray\EloquentModelAnalyzer\Contracts;

use Illuminate\Support\Collection;

interface Detector
{
    public function __construct($model);

    public function discover(): Collection;
}
