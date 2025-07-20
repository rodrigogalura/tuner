<?php

namespace Laradigs\Tweaker\Projection;

use function RGalura\ApiIgniter\filter_explode;
use Illuminate\Database\Eloquent\Model;

class ProjectionField extends Projection
{
    private static $ignoreIfFieldsAreEmpty = false;

    public function __construct(
        Model $model,
        array $projectableFields,
        array $definedFields,
        private mixed $clientInput,
    ) {
        parent::__construct($model, $projectableFields, $definedFields);
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

    public static function ignoreIfFieldsAreEmpty()
    {
        static::$ignoreIfFieldsAreEmpty = true;
    }

    public function project()
    {
        $this->validate();
        $this->convertClientInputToArray();

        return $this->clientInput === ['*']
            ? $this->projectableFields
            : $this->truthTable->intersect($this->projectableFields, $this->clientInput);
    }
}
