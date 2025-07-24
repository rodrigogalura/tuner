<?php

namespace Laradigs\Tweaker\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class LinearArray implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_array($value)) {
            $multiArray = false;
            while ($current = current($value)) {
                if (is_array($current)) {
                    $multiArray = true;
                    break;
                }

                next($value);
            }

            if ($multiArray) {
                $fail('The :attribute must be a linear array.');
            }

            goto pass;
        }

        $fail('The :attribute must be array.');

        pass:
    }
}
