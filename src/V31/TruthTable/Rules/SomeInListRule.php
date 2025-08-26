<?php

namespace Laradigs\Tweaker\V31\TruthTable\Rules;

use function RGalura\ApiIgniter\filter_explode;
use Laradigs\Tweaker\V31\TruthTable\Rule;

class SomeInListRule extends Rule
{
    public function __construct(private array $list, int $errorCode)
    {
        parent::__construct($errorCode);
    }

    public function passed(string $subject)
    {
        return ! empty(array_intersect(filter_explode($subject), $this->list));
    }
}
