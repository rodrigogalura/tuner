<?php

namespace Laradigs\Tweaker\V31\Projection;

use Laradigs\Tweaker\V31\TruthTable\TruthTable;

class Projection
{
    private TruthTable $truthTable;

    public function __construct(array $columns)
    {
        // $this->truthTable = new TruthTable($columns);
    }

    public function matrix()
    {

    }
}
