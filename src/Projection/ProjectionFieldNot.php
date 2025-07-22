<?php

namespace Laradigs\Tweaker\Projection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

use function RGalura\ApiIgniter\filter_explode;

class ProjectionFieldNot extends Projection
{
    private static $ignoreIfFieldNotIsAsterisk = false;

    public function __construct(
        Model $model,
        array $projectableFields,
        array $definedFields,
        mixed $clientInput,
        array $projectionConfig = ['except_key' => 'fields!'],
    ) {
        parent::__construct($model, $projectableFields, $definedFields, Arr::get($clientInput, $projectionConfig['except_key']));
    }

    protected function validate()
    {
        parent::prerequisites();

        throw_if(static::$ignoreIfFieldNotIsAsterisk && $this->clientInput === '*', NoActionWillPerformException::class);

        parent::validate();
    }

    public function isUsed()
    {
        return ! is_null($this->clientInput);
    }

    public function project()
    {
        $this->validate();

        $this->clientInput = filter_explode($this->clientInput);

        return $this->truthTable->except($this->projectableFields, $this->clientInput);
    }

    public static function ignoreIfFieldNotIsAsterisk()
    {
        static::$ignoreIfFieldNotIsAsterisk = true;
    }
}
