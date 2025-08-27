<?php

namespace Laradigs\Tweaker\V31\TruthTable\Rules;

use Laradigs\Tweaker\V31\ErrorCodes;
use Laradigs\Tweaker\V31\Projection\Error;
use Laradigs\Tweaker\V31\TruthTable\Rule;

use function RGalura\ApiIgniter\filter_explode;

class SomeInListRule extends Rule
{
    public function __construct(private array $list, Error|ErrorCodes $e)
    {
        parent::__construct($e);
    }

    public function passed(string $subject)
    {
        return ! empty(array_intersect(filter_explode($subject), $this->list));
    }
}
