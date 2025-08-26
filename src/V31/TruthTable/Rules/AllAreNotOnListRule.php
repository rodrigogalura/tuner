<?php

namespace Laradigs\Tweaker\V31\TruthTable\Rules;

use function RGalura\ApiIgniter\filter_explode;
use Laradigs\Tweaker\V31\TruthTable\RuleInterface;

class AllAreNotOnListRule implements RuleInterface {
    public function __construct(private array $list, private $errorCode)
    {
        //
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function handle(string $subject)
    {
        // return ! empty (array_diff(filter_explode($subject), $this->list));
    }
}
