<?php

namespace Laradigs\Tweaker\Projection;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use function RGalura\ApiIgniter\filter_explode;

class ProjectionField extends Projection
{
    private static $ignoreIfFieldsAreEmpty = false;

    public function __construct(
        Model $model,
        array $projectableFields,
        array $definedFields,
        private mixed $clientInput,
        private array $projectionConfig = ['intersect_key' => 'fields'],
    ) {
        parent::__construct($model, $projectableFields, $definedFields);

        $this->clientInput = Arr::get($this->clientInput, $this->projectionConfig['intersect_key']);
    }

    private function convertClientInputToArray()
    {
        $this->clientInput = filter_explode($this->clientInput);
    }

    protected function validate()
    {
        // Make sure client input is valid
        if (!is_string($this->clientInput)) {
            throw new NoActionWillPerformException;
        }

        if (static::$ignoreIfFieldsAreEmpty && empty($clientInput)) {
            throw new NoActionWillPerformException;
        }

        parent::validate();
    }

    public function isUsed()
    {
        return !is_null($this->clientInput);
    }

    public function project()
    {
        $this->validate();
        $this->convertClientInputToArray();

        return $this->clientInput === ['*']
            ? $this->projectableFields
            : $this->truthTable->intersect($this->projectableFields, $this->clientInput);
    }

    public static function ignoreIfFieldsAreEmpty()
    {
        static::$ignoreIfFieldsAreEmpty = true;
    }
}
