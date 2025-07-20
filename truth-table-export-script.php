<?php

class CSVToArray
{
    private array $fileMethod = [
        'truth-table.csv' => 'truthTable',
        'projection-truth-table.csv' => 'projection',
        'search-truth-table.csv' => 'search',
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

    public function projection()
    {
        $rowCounter = 1;

        $CELL_ROW_STARTS_AT = 11;
        $CELL_COLS_LENGTH = 5;

        $PREREQUISITES_CODES = [1, 2];

        return $this->readFileAndReturnData(function (&$data, $row) use (
            &$rowCounter,
            $CELL_ROW_STARTS_AT,
            $CELL_COLS_LENGTH,
            $PREREQUISITES_CODES
        ) {
            if ($rowCounter++ < $CELL_ROW_STARTS_AT) {
                return; // skip
            }

            if ($row[0] == '') { // skip no value row
                return; //
            }

            // convert 'empty' string to ''
            for ($i = 0; $i < $CELL_COLS_LENGTH; $i++) {
                static::convertToEmptyStringIfEmptyValue($row[$i]);
            }

            $result_fields = $row[3];
            $result_fields_not = $row[4];

            if (in_array($result_fields, $PREREQUISITES_CODES) || in_array($result_fields_not, $PREREQUISITES_CODES)) {
                return; // skip;
            }

            $projectableFields = $row[0];
            $definedFields = $row[1];
            $clientInput = $row[2];

            $data[] = [
                'projectableFields' => explode_sanitized($projectableFields),
                'definedFields' => explode_sanitized($definedFields),
                'clientInput' => $clientInput,
                'resultFields' => explode_sanitized($result_fields),
                'resultFieldsNot' => explode_sanitized($result_fields_not),
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

            // if (! is_numeric($result_fields = $row[7])) {
            //     $data[] = [
            //         'searchableFields' => explode_sanitized($row[0]),

            //         'search_fields' => $row[1],
            //         'search_value_no_wildcard' => $row[2],
            //         'search_value_both_wildcard' => $row[3],
            //         'search_value_left_wildcard' => $row[4],
            //         'search_value_right_wildcard' => $row[5],

            //         'result_fields' => $result_fields,
            //         'result_value_unit_no_wildcard' => $row[8],
            //         'result_value_unit_both_wildcard' => $row[9],
            //         'result_value_unit_left_wildcard' => $row[10],
            //         'result_value_unit_right_wildcard' => $row[11],

            //         'result_value_feature_no_wildcard' => static::isEmpty($row[12]) ? [] : explode_sanitized($row[12]),
            //         'result_value_feature_both_wildcard' => static::isEmpty($row[13]) ? [] : explode_sanitized($row[13]),
            //         'result_value_feature_left_wildcard' => static::isEmpty($row[14]) ? [] : explode_sanitized($row[14]),
            //         'result_value_feature_right_wildcard' => static::isEmpty($row[15]) ? [] : explode_sanitized($row[15]),
            //     ];
            // }
        });
    }
}

function explode_sanitized(string $str, string $delimiter = ',')
{
    return array_filter(array_map('trim', explode($delimiter, $str)));
}

// $csvToArray = new CSVToArray('truth-table.csv');
// echo $csvToArray->export();

$csvToArray = new CSVToArray('search-truth-table.csv');
echo $csvToArray->export();

/*
    Run this script using the command:
    php truth-table-export-script.php | pbcopy
 */
