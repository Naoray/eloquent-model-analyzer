<?php

namespace Naoray\EloquentModelAnalyzer\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface Detector
{
    public function __construct(Model $model);

    public function analyze(): Collection;
}
