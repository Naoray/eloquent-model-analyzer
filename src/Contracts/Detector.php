<?php

namespace Naoray\EloquentModelAnalyzer\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Detector
{
    public function analyze(Model $resource): array;
}
