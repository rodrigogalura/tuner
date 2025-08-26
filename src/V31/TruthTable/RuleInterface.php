<?php

namespace Laradigs\Tweaker\V31\TruthTable;

interface RuleInterface
{
    public function getErrorCode();

    public function handle(string $subject);
}
