<?php

namespace Laradigs\Tweaker\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

use function RGalura\ApiIgniter\filter_explode;

class ExceptProjectionRule implements ValidationRule
{
    const EXCLUDE_ALL_ERROR_MESSAGE = 'The :attribute is invalid. Excluding all fields is not allowed.';

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
        try {
            if (trim($value) === '*') {
                throw new \Exception(static::EXCLUDE_ALL_ERROR_MESSAGE);
            }

            $remainingFields = array_diff($this->availableFields, filter_explode($value));

            if (empty($remainingFields)) {
                throw new \Exception(static::EXCLUDE_ALL_ERROR_MESSAGE);
            }
        } catch (\Exception $e) {
            $fail($e->getMessage());
        }
    }
}
