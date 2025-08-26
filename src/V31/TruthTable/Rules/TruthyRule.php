<?php

namespace Laradigs\Tweaker\V31\TruthTable\Rules;

use Laradigs\Tweaker\V31\TruthTable\Rule;

class TruthyRule extends Rule
{
    public function passed(string $subject)
    {
        return ! empty($subject);
    }
}
