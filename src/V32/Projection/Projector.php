<?php

namespace Laradigs\Tweaker\V32\Projection;

use Illuminate\Database\Eloquent\Builder;
use Laradigs\Tweaker\V32\Projection\ErrorEnum as Error;
use Laradigs\Tweaker\V32\ValueObjects\ProjectionInput;

use function RGalura\ApiIgniter\filter_explode;

class Projector
{
    private $inputValue;

    public function __construct(
        private Builder $builder,
        private array $visibleColumns,
        private array $projectableColumns,
        private array $definedColumns,
        private ProjectionInput $input,
        private bool $strict = false
    ) {
        $this->inputValue = $input->getValue();
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

        $this->assignIfAsterisk($this->projectableColumns, $this->visibleColumns);
        $this->throwIfNotInColumns($this->projectableColumns, Error::P_NotInColumns);

        Intersect::from($this->visibleColumns)->toR($this->projectableColumns);
    }

    private function validateDefined()
    {
        throw_if(empty($this->definedColumns), Error::Q_LaravelDefaultError->exception());

        $this->assignIfAsterisk($this->definedColumns, $this->visibleColumns);
        $this->throwIfNotInColumns($this->definedColumns, Error::Q_NotInColumns);

        Intersect::from($this->definedColumns)->toR($this->projectableColumns);

        throw_if(empty($this->projectableColumns), Error::Q_NotInProjectable->exception());
    }

    private function validateInput()
    {
        $input = $this->input;

        if ($input->used() && $this->strict) {
            if ($input->intersectIsUse()) {
                $e = Error::R_IncludeUnknownColumn;
            }

            if ($input->exceptIsUse()) {
                $e = Error::R_ExcludeUnknownColumn;
            }

            throw_if(
                $projected = Except::from(filter_explode($this->inputValue))
                    ->to($this->projectableColumns)
                    ->get(),
                $e->exception(invalidColumns: $projected)
            );
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
        throw_if($diff = array_diff($fields, $this->visibleColumns), $e->exception(invalidColumns: $diff));
    }

    private function intersect()
    {
        $input = filter_explode($this->inputValue);

        return $input === ['*']
            ? $this->projectableColumns
            : Intersect::from($this->projectableColumns)->to($input)->get();
    }

    private function except()
    {
        return Except::from($this->projectableColumns)
            ->to(filter_explode($this->inputValue))
            ->get();
    }

    public static function run(
        Builder $builder,
        array $visibleColumns,
        array $projectableColumns,
        array $definedColumns,
        ProjectionInput $input,
        bool $strict = false
    ) {
        if (! is_null($projectionType = $input->used())) {
            $projection = new self(
                $builder,
                $visibleColumns,
                $projectableColumns,
                $definedColumns,
                $input,
                $strict
            );

            $projection->validate();

            $columns = $projection->{$projectionType}();
            throw_if(count($columns) === 0, Error::ProjectedColumnIsEmpty->exception('empty right?'));

            $projection->builder->select($columns);
        }
    }
}
