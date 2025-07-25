<?php

namespace Laradigs\Tweaker\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

use function RGalura\ApiIgniter\filter_explode;

class ExceptProjectionRule implements ValidationRule
{
    public function __construct(private array $availableFields)
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
        if (trim($value) === '*') {
            $fail('Invalid :attribute: excluding all fields is not allowed.');
        }

        $remainingFields = array_diff($this->availableFields, filter_explode($value));

        if (empty($remainingFields)) {
            $fail('Invalid :attribute: excluding all fields is not allowed.');
        }
    }
}
