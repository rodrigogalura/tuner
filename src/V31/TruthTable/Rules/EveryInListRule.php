<?php

namespace Laradigs\Tweaker\V31\TruthTable\Rules;

use function RGalura\ApiIgniter\filter_explode;
use Laradigs\Tweaker\V31\TruthTable\Rule;

class EveryInListRule extends Rule
{
    public function __construct(private array $list, int $errorCode)
    {
        parent::__construct($errorCode);
    }

    public function passed(string $subject)
    {
        dump($this->list);
        dump(filter_explode($subject));
        dd(array_diff($this->list, filter_explode($subject)));
        return empty(array_diff($this->list, filter_explode($subject)));
    }
}
