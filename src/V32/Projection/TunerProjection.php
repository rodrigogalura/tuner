<?php

namespace Laradigs\Tweaker\V32\Projection;

use Illuminate\Database\Eloquent\Builder;
use function RGalura\ApiIgniter\filter_explode;
use Laradigs\Tweaker\V32\ValueObjects\ProjectionInput;
use Laradigs\Tweaker\V32\Projection\ErrorEnum as Error;

class TunerProjection
{
    private $inputValue;

    public function __construct(
        private Builder $builder,
        protected array $visibleColumns,
        protected array $projectableColumns,
        protected array $definedColumns,
        private ProjectionInput $input,
    )
    {
        $this->inputValue = $input->getValue();
        $this->validate();
    }

    private function validate()
    {
        $this->validateProjectable();
        $this->validateDefined();
        $this->validateInput();
    }

    private function validateProjectable()
    {
        throw_if(empty($this->projectableColumns), Error::P_Disabled->exception());

        // assign_if(['*'], $this->projectableColumns, newValue: $this->visibleColumns);
        $this->assignIfAsterisk($this->projectableColumns, $this->visibleColumns);
        $this->throwIfNotInColumns($this->projectableColumns, Error::P_NotInColumns);

        Intersect::from($this->visibleColumns)->toR($this->projectableColumns);

        // $this->truthTable->intersectToAllItems($this->projectableColumns);
    }

    private function validateDefined()
    {
        throw_if(empty($this->definedColumns), Error::Q_LaravelDefaultError->exception());

        // assign_if(['*'], $this->definedColumns, newValue: $this->visibleColumns);
        $this->assignIfAsterisk($this->definedColumns, $this->visibleColumns);
        $this->throwIfNotInColumns($this->definedColumns, Error::Q_NotInColumns);

        Intersect::from($this->definedColumns)->toR($this->projectableColumns);

        // $this->projectableColumns = ($this->intersect)($this->projectableColumns, $this->definedColumns);
        throw_if(empty($this->projectableColumns), Error::Q_NotInProjectable->exception());
    }

    private function validateInput()
    {
        if ($this->input->exceptIsUse()) {
            $input = filter_explode($this->inputValue);
            $allColumnsAreExcluded = count(array_diff($this->projectableColumns, $input)) === 0;

            throw_if($input === ['*'] || $allColumnsAreExcluded, Error::R_CannotExcludeAll->exception());
        }
    }

    private function assignIfAsterisk(array &$var, $newValue)
    {
        if ($var === ['*']) {
            $var = $newValue;
        }
    }

    private function throwIfNotInColumns(array $fields, Error $e)
    {
        throw_if($diff = array_diff($this->visibleColumns, $fields), $e->exception(invalidColumns: $diff));
    }

    private function intersect()
    {
        $input = filter_explode($this->inputValue);
        return $input === ['*']
            ? $this->projectableColumns
            : Intersect::from($this->projectableColumns)->to($input);
    }

    private function except()
    {
        $input = filter_explode($this->inputValue);
        return Except::from($this->projectableColumns)->to($input);
    }

    public static function process(
        Builder $builder,
        array $visibleColumns,
        array $projectableColumns,
        array $definedColumns,
        ProjectionInput $input,
    )
    {
        $projection = new self(
            $builder,
            $visibleColumns,
            $projectableColumns,
            $definedColumns,
            $input,
        );

        if (! is_null($method = $projection->input->used())) {
            $columns = $projection->{$method}();
            $projection->builder->select($columns);
        }
    }
}
