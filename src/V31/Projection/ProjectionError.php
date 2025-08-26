<?php

namespace Laradigs\Tweaker\V31\Projection;

use Exception;

enum ProjectionError : int
{
    case P_Disabled = 2;
    case P_NotInColumns = 3;

    case Q_LaravelDefaultError = 4;
    case Q_NotInColumns = 5;
    case Q_NotInProjectable = 6;

    public function exception(string $errorMessage = '')
    {
        return new Exception($errorMessage, $this->value);
    }

    public function notInColumnsException(array $columns = [])
    {
        $invalidColumns = array_values($columns);

        $errorMessage = match($this) {
            ProjectionError::P_NotInColumns,
            ProjectionError::Q_NotInColumns => count($invalidColumns) === 1
                ? "The column '{$invalidColumns[0]}' is not a valid column."
                : "The columns '".implode("', '", $invalidColumns)."' are not valid columns.",
            default => ''
        };

        return $this->exception($errorMessage);
    }

    public function exception2(string $errorMessage = '', array $invalidColumns = [])
    {
        if (count($invalidColumns) > 0) {
            $errorMessage = match($this) {
                ProjectionError::P_NotInColumns,
                ProjectionError::Q_NotInColumns,
                ProjectionError::Q_NotInProjectable =>
                    count($invalidColumns) === 1
                    ? "The column '{$invalidColumns[0]}' is not a valid column."
                    : "The columns '".implode("', '", $invalidColumns)."' are not valid columns.",
                default => $errorMessage
            };
        }

        return new Exception($errorMessage, code: $this->value);
    }
}
