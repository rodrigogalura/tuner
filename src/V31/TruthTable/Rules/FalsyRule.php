<?php

namespace Laradigs\Tweaker\V31\TruthTable\Rules;

use Laradigs\Tweaker\V31\TruthTable\RuleInterface;

class FalsyRule implements RuleInterface {
    public function __construct(private $errorCode)
    {
        //
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function handle(string $subject)
    {
        return empty($item);
    }
}
