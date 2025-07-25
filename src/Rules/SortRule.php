<?php

namespace Laradigs\Tweaker\Rules;

use Illuminate\Translation\PotentiallyTranslatedString;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Laradigs\Tweaker\Sort\Sort;

use function RGalura\ApiIgniter\validate2;

class SortRule implements ValidationRule
{
    public function __construct(private string $key, private array $availableFields)
    {
        //
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string=):PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            if (! is_array($value)) {
                throw new \Exception('The :attribute must be array.');
            }

            $allowedDirectionValues = array_merge(Sort::ASCENDING_VALUES, Sort::DESCENDING_VALUES);

            validate2(
                [
                    $this->key => $value,
                    "{$this->key} fields" => array_keys($value),
                    "{$this->key} direction" => array_values($value),
                ],

                [
                    $this->key => new LinearArray,
                    "{$this->key} fields" => ['array', Rule::in($this->availableFields)],
                    "{$this->key} direction" => ['array', Rule::in($allowedDirectionValues)],
                ],

                [
                    "{$this->key} fields.in" => 'The :attribute is invalid. Allowed values: '.implode(', ', $this->availableFields),
                    "{$this->key} direction.in" => 'The :attribute is invalid. Allowed values: '.implode(', ', array_map(fn ($direction): string => "'{$direction}'", $allowedDirectionValues)),
                ]
            );
        } catch (\Exception $e) {
            $fail($e->getMessage());
        }
    }
}
