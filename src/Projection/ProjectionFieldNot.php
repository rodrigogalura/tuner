<?php

namespace Laradigs\Tweaker\Projection;

use function RGalura\ApiIgniter\filter_explode;
use Illuminate\Database\Eloquent\Model;

class ProjectionFieldNot extends Projection
{
    private static $ignoreIfFieldNotIsAsterisk = false;

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

        if (static::$ignoreIfFieldNotIsAsterisk && empty($this->clientInput)) {
            throw new NoActionWillPerformException;
        }

        parent::validate();
    }

    public static function ignoreIfFieldNotIsAsterisk()
    {
        static::$ignoreIfFieldNotIsAsterisk = true;
    }

    public function project()
    {
        $this->validate();
        $this->convertClientInputToArray();

        return $this->truthTable->except($this->projectableFields, $this->clientInput);
    }
}
