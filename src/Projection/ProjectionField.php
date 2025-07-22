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
        mixed $clientInput,
        array $projectionConfig = ['intersect_key' => 'fields'],
    ) {
        parent::__construct($model, $projectableFields, $definedFields, Arr::get($clientInput, $projectionConfig['intersect_key']));
    }

    protected function validate()
    {
        parent::prerequisites();

        throw_if(static::$ignoreIfFieldsAreEmpty && empty($this->clientInput), NoActionWillPerformException::class);

        parent::validate();
    }

    public function isUsed()
    {
        return !is_null($this->clientInput);
    }

    public function project()
    {
        $this->validate();

        $this->clientInput = filter_explode($this->clientInput);

        return $this->clientInput === ['*']
            ? $this->projectableFields
            : $this->truthTable->intersect($this->projectableFields, $this->clientInput);
    }

    public static function ignoreIfFieldsAreEmpty()
    {
        static::$ignoreIfFieldsAreEmpty = true;
    }
}
