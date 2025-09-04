<?php

namespace Laradigs\Tweaker\V32\Projection;

use Exception;

enum ErrorEnum: int
{
    case P_Disabled = 2;
    case P_NotInColumns = 3;

    case Q_LaravelDefaultError = 4;
    case Q_NotInColumns = 5;
    case Q_NotInProjectable = 6;

    case R_IncludeUnknownColumn = 7;
    case R_ExcludeUnknownColumn = 8;

    case ProjectedColumnIsEmpty = 9;

    public function exception(string $errorMessage = '', array $invalidColumns = [])
    {
        if (count($invalidColumns) > 0) {
            $columns = array_values($invalidColumns);

            $errorMessage = match ($this) {
                ErrorEnum::P_NotInColumns => count($columns) === 1
                    ? "The projectable column '{$columns[0]}' is not a valid projectable column."
                    : "The projectable columns '".implode("', '", $columns)."' are not valid projectable columns.",

                ErrorEnum::Q_NotInColumns,
                ErrorEnum::R_IncludeUnknownColumn,
                ErrorEnum::R_ExcludeUnknownColumn => count($columns) === 1
                    ? "The column '{$columns[0]}' is not a valid column."
                    : "The columns '".implode("', '", $columns)."' are not valid columns.",

                ErrorEnum::Q_NotInProjectable => count($columns) === 1
                    ? "The projectable column '{$columns[0]}' is not a valid column."
                    : "The projectable columns '".implode("', '", $columns)."' are not valid columns.",
                default => $errorMessage
            };
        }

        return new Exception($errorMessage, $this->getCode());
    }

    public function getCode()
    {
        return $this->value;
    }
}
