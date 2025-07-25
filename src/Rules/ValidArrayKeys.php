<?php

namespace Laradigs\Tweaker\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidArrayKeys implements ValidationRule
{
    public function __construct(private array $allowedKeys)
    {
        //
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=):PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_array($value)) {
            if (! empty($diff = array_diff(array_keys($value), $this->allowedKeys))) {
                $fail('The :attribute contains invalid keys. It must be one of the valid keys: '.implode(', ', $this->allowedKeys));
            }

            goto pass;
        }

        $fail('The :attribute must be array.');

        pass:
    }
}
