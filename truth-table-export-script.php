<?php

class CSVToArray
{
    private array $fileMethod = [
        'truth-table/truth-table.csv' => 'truthTable',
        // 'truth-table/projection-truth-table.csv' => 'intersectProjection',
        'truth-table/projection-truth-table.csv' => 'exceptProjection',
        'truth-table/search-truth-table.csv' => 'search',
        'truth-table/sort-truth-table.csv' => 'sort',
    ];

    private readonly array $data;

    const EMPTY_VALUE = 'empty';

    public function __construct(
        private $csvPath,
        private $delimiter = ',',
        private $enclosure = '"',
        private $escape = '\\'
    ) {
        if (! file_exists($this->csvPath) || ! is_readable($this->csvPath)) {
            throw new Exception('CSV file not found or not readable.');
        }

        if (! in_array($csvPath, array_keys($this->fileMethod))) {
            throw new Exception('Invalid csv file provided.');
        }

        $method = $this->fileMethod[$csvPath];

        $this->data = $this->{$method}();
    }

    private function readFileAndReturnData(callable $callback)
    {
        $data = [];
        if (($handle = fopen($this->csvPath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 0, $this->delimiter, $this->enclosure, $this->escape)) !== false) {
                $callback($data, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    private static function isEmpty(string $str)
    {
        return $str === static::EMPTY_VALUE;
    }

    private static function convertToEmptyStringIfEmptyValue(string &$str)
    {
        if ($str === static::EMPTY_VALUE) {
            $str = '';
        }
    }

    public function export()
    {
        $export = var_export($this->data, true);
        $export = preg_replace("/^(\s*)array\s*\(/m", '$1[', $export);
        $export = preg_replace("/\)(,?)$/m", ']$1', $export);

        return $export;
    }

    public function truthTable()
    {
        $rowIndex = 1;

        return $this->readFileAndReturnData(function (&$data, $row) use (&$rowIndex) {
            if ($rowIndex++ <= 2) { // skip 2 rows (headers)
                return;
            }

            // convert 'empty' string to []
            for ($i = 0; $i <= 3; $i++) {
                if (static::isEmpty($row[$i])) {
                    $row[$i] = '';
                }
            }

            // intersect
            $data[] = [
                'p' => explode_sanitized($row[0]),
                'q' => explode_sanitized($row[1]),
                'p_INTERSECT_q' => $row[2] == '0' ? false : explode_sanitized($row[2]),
                'p_EXCEPT_q' => $row[3] == '0' ? false : explode_sanitized($row[3]),
            ];
        });
    }

    public function intersectProjection()
    {
        $rowCounter = 1;

        $CELL_ROW_STARTS_AT = 3;
        $CELL_COLS_LENGTH = 4;

        return $this->readFileAndReturnData(function (&$data, $row) use (
            &$rowCounter,
            $CELL_ROW_STARTS_AT,
            $CELL_COLS_LENGTH,
        ) {
            if ($rowCounter++ < $CELL_ROW_STARTS_AT) {
                return; // skip
            }

            if ($row[at('A')] == '') { // skip no value row
                return; //
            }

            // convert 'empty' string to ''
            for ($i = 0; $i < $CELL_COLS_LENGTH; $i++) {
                static::convertToEmptyStringIfEmptyValue($row[$i]);
            }

            $projectableFields = $row[at('A')];
            $definedFields = $row[at('B')];
            $clientInput = $row[at('C')];
            $intersectResult = $row[at('D')];

            if ($intersectResult === 'throw d invalid') {
                return; // skip;
            }

            $data[] = [
                'projectableFields' => explode_sanitized($projectableFields),
                'definedFields' => explode_sanitized($definedFields),
                'clientInput' => $clientInput,
                'intersectResult' => explode_sanitized($intersectResult),
            ];
        });
    }

    public function exceptProjection()
    {
        $rowCounter = 1;

        $CELL_ROW_STARTS_AT = 3;
        $CELL_COLS_LENGTH = 9;

        return $this->readFileAndReturnData(function (&$data, $row) use (
            &$rowCounter,
            $CELL_ROW_STARTS_AT,
            $CELL_COLS_LENGTH,
        ) {
            if ($rowCounter++ < $CELL_ROW_STARTS_AT) {
                return; // skip
            }

            if ($row[at('F')] == '') { // skip no value row
                return; //
            }

            // convert 'empty' string to ''
            for ($i = 0; $i < $CELL_COLS_LENGTH; $i++) {
                static::convertToEmptyStringIfEmptyValue($row[$i]);
            }

            $projectableFields = $row[at('F')];
            $definedFields = $row[at('G')];
            $clientInput = $row[at('H')];
            $exceptResult = $row[at('I')];

            if ($exceptResult === 'throw d invalid' || $exceptResult == 422) {
                return; // skip;
            }

            $data[] = [
                'projectableFields' => explode_sanitized($projectableFields),
                'definedFields' => explode_sanitized($definedFields),
                'clientInput' => $clientInput,
                'exceptResult' => explode_sanitized($exceptResult),
            ];
        });
    }

    public function search()
    {
        $rowCounter = 1;

        $CELL_ROW_STARTS_AT = 4;
        $CELL_ROW_CLIENT_KEYWORD_STARTS_AT = 37;
        $CELL_COLS_LENGTH = 3;

        $PREREQUISITES_CODES = [1, 2];

        return $this->readFileAndReturnData(function (&$data, $row) use (
            &$rowCounter,
            $CELL_ROW_STARTS_AT,
            $CELL_ROW_CLIENT_KEYWORD_STARTS_AT,
            $CELL_COLS_LENGTH,
            $PREREQUISITES_CODES
        ) {
            if ($rowCounter++ < $CELL_ROW_STARTS_AT) {
                return; // skip
            }

            if ($row[1] == '') { // skip no value row
                return; //
            }

            // convert 'empty' string to ''
            for ($i = 0; $i < $CELL_COLS_LENGTH; $i++) {
                static::convertToEmptyStringIfEmptyValue($row[$i]);
            }

            $searchableFields = $row[0];

            if ($rowCounter < $CELL_ROW_CLIENT_KEYWORD_STARTS_AT) {
                $clientFields = $row[1];
                $resultFields = $row[2];

                if (in_array($resultFields, $PREREQUISITES_CODES)) {
                    return; // skip;
                }

                // fields
                $data[] = [
                    'searchableFields' => explode_sanitized($searchableFields),
                    'clientFields' => $clientFields,
                    'resultFields' => $resultFields,
                ];
            } else {
                $clientKeyword = $row[1];
                $resultKeyword = $row[2];

                // keyword
                $data[] = [
                    'clientKeyword' => $clientKeyword,
                    'resultKeyword' => $resultKeyword,
                ];
            }
        });
    }

    public function sort()
    {
        $rowCounter = 1;

        $CELL_ROW_STARTS_AT = 4;
        $CELL_ROW_CLIENT_KEYWORD_STARTS_AT = 20;
        $CELL_COLS_LENGTH = 3;

        return $this->readFileAndReturnData(function (&$data, $row) use (
            &$rowCounter,
            $CELL_ROW_STARTS_AT,
            $CELL_ROW_CLIENT_KEYWORD_STARTS_AT,
            $CELL_COLS_LENGTH,
        ) {
            if ($rowCounter++ < $CELL_ROW_STARTS_AT) {
                return; // skip
            }

            if (in_array($row[at('C')], ['', 422])) { // skip no value row
                return; //
            }

            // convert 'empty' string to ''
            for ($i = 0; $i < $CELL_COLS_LENGTH; $i++) {
                static::convertToEmptyStringIfEmptyValue($row[$i]);
            }

            $sortableFields = $row[at('A')];

            if ($rowCounter < $CELL_ROW_CLIENT_KEYWORD_STARTS_AT) {
                $clientFields = $row[at('B')];
                $resultFields = $row[at('C')];

                // fields
                $data[] = [
                    'sortableFields' => explode_sanitized($sortableFields),
                    'clientFields' => explode_sanitized($clientFields),
                    'resultFields' => explode_sanitized($resultFields),
                ];
            } else {
                $clientDirection = $row[at('B')];
                $resultDirection = $row[at('C')];

                // keyword
                $data[] = [
                    'clientDirection' => $clientDirection,
                    'resultDirection' => $resultDirection,
                ];
            }
        });
    }
}

/**
 * Get index by alphabet
 *
 * @param  string  $char  Single character
 */
function at($char)
{
    $char = strtoupper($char);
    $alphabet = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');

    return array_search($char, $alphabet);
}

function explode_sanitized(string $str, string $delimiter = ',')
{
    return array_filter(array_map('trim', explode($delimiter, $str)));
}

// $csvToArray = new CSVToArray('truth-table/truth-table.csv');
// echo $csvToArray->export();

// $csvToArray = new CSVToArray('truth-table/projection-truth-table.csv');
// $csvToArray = new CSVToArray('truth-table/search-truth-table.csv');
$csvToArray = new CSVToArray('truth-table/sort-truth-table.csv');
echo $csvToArray->export();

/*
    Run this script using the command:
    php truth-table-export-script.php | pbcopy
 */
