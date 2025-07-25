<?php

namespace Laradigs\Tweaker\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class LinearArray implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=):PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            if (! is_array($value)) {
                throw new \Exception('The :attribute must be array.');
            }

            $multiArray = false;
            while ($current = current($value)) {
                if (is_array($current)) {
                    $multiArray = true;
                    break;
                }

                next($value);
            }

            if ($multiArray) {
                throw new \Exception('The :attribute must be a linear array.');
            }
        } catch (\Exception $e) {
            $fail($e->getMessage());
        }
    }
}
