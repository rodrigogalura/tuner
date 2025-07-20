<?php

namespace Laradigs\Tweaker\Projection;

use Illuminate\Database\Eloquent\Model;

class ProjectionFieldNot extends Projection
{
    private static $ignoreIfFieldNotIsAsterisk = false;

    public function __construct(
        Model $model,
        array $projectableFields,
        array $definedFields,
        private array $clientInput,
    ) {
        parent::__construct($model, $projectableFields, $definedFields);
    }

    protected function validate()
    {
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

        return $this->truthTable->except($this->projectableFields, $this->clientInput);
    }
}
