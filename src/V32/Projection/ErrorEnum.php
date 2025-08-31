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

    case R_CannotExcludeAll = 7;

    public function exception(string $errorMessage = '', array $invalidColumns = [])
    {
        if (count($invalidColumns) > 0) {
            $errorMessage = match ($this) {
                Error::P_NotInColumns,
                Error::Q_NotInColumns => count($invalidColumns) === 1
                    ? "The column '{$invalidColumns[0]}' is not a valid column."
                    : "The columns '".implode("', '", $invalidColumns)."' are not valid columns.",

                Error::Q_NotInProjectable => count($invalidColumns) === 1
                    ? "The projectable column '{$invalidColumns[0]}' is not a valid column."
                    : "The projectable columns '".implode("', '", $invalidColumns)."' are not valid columns.",
                default => $errorMessage
            };
        }

        return new Exception($errorMessage, code: $this->value);
    }
}
