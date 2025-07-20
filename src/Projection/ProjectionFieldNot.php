<?php

namespace Laradigs\Tweaker\Projection;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use function RGalura\ApiIgniter\filter_explode;

class ProjectionFieldNot extends Projection
{
    private static $ignoreIfFieldNotIsAsterisk = false;

    public function __construct(
        Model $model,
        array $projectableFields,
        array $definedFields,
        private mixed $clientInput,
        private array $projectionConfig = ['except_key' => 'fields!'],
    ) {
        parent::__construct($model, $projectableFields, $definedFields);

        $this->clientInput = Arr::get($this->clientInput, $this->projectionConfig['except_key']);
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

    public function isUsed()
    {
        return !is_null($this->clientInput);
    }

    public function project()
    {
        $this->validate();
        $this->convertClientInputToArray();

        return $this->truthTable->except($this->projectableFields, $this->clientInput);
    }

    public static function ignoreIfFieldNotIsAsterisk()
    {
        static::$ignoreIfFieldNotIsAsterisk = true;
    }
}
