<?php

class CSVExporter
{
    private array $fileMethod = [
        'intersect-projection-non-strict.csv' => 'intersectProjectionNonStrict',
        'intersect-projection-strict.csv' => 'intersectProjectionStrict',

        'except-projection-non-strict.csv' => 'exceptProjectionNonStrict',
        // 'except-projection-strict.csv' => 'exceptProjectionNonStrict',
    ];

    private readonly array $data;

    public function __construct(
        private $csvPath,
        private $delimiter = ',',
        private $enclosure = '"',
        private $escape = '\\'
    ) {
        if (! file_exists($this->csvPath) || ! is_readable($this->csvPath)) {
            throw new Exception('CSV file not found or not readable.');
        }

        $csvFile = basename($csvPath);

        if (! in_array($csvFile, array_keys($this->fileMethod))) {
            throw new Exception('Invalid csv file provided.');
        }

        $method = $this->fileMethod[$csvFile];

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

    public function export()
    {
        $export = var_export($this->data, true);
        $export = preg_replace("/^(\s*)array\s*\(/m", '$1[', $export);
        $export = preg_replace("/\)(,?)$/m", ']$1', $export);

        return $export;
    }

    public function intersectProjectionNonStrict()
    {
        $rowCounter = 1;

        $CELL_ROW_STARTS_AT = 3;
        $CELL_COLS_LENGTH = 5;

        return $this->readFileAndReturnData(function (&$data, $row) use (
            &$rowCounter,
            $CELL_ROW_STARTS_AT,
        ) {
            if ($rowCounter++ < $CELL_ROW_STARTS_AT) {
                return; // skip
            }

            if ($row[at('A')] == '') { // skip no value row
                return; //
            }

            $projectableFields = $row[at('A')];
            $definedFields = $row[at('B')];
            $clientInput = $row[at('C')];
            $intersectResultNonStrict = $row[at('D')];

            if ($intersectResultNonStrict === 'invalid defined') {
                return; // skip;
            }

            $data[] = [
                'projectableFields' => explode_sanitized($projectableFields),
                'definedFields' => explode_sanitized($definedFields),
                'clientInput' => $clientInput,
                'intersectResultNonStrict' => explode_sanitized($intersectResultNonStrict),
            ];
        });
    }

    public function intersectProjectionStrict()
    {
        $rowCounter = 1;

        $CELL_ROW_STARTS_AT = 3;
        $CELL_COLS_LENGTH = 5;

        return $this->readFileAndReturnData(function (&$data, $row) use (
            &$rowCounter,
            $CELL_ROW_STARTS_AT,
        ) {
            if ($rowCounter++ < $CELL_ROW_STARTS_AT) {
                return; // skip
            }

            if ($row[at('A')] == '') { // skip no value row
                return; //
            }

            $projectableFields = $row[at('A')];
            $definedFields = $row[at('B')];
            $clientInput = $row[at('C')];
            $intersectResultStrict = $row[at('D')];

            if (in_array($intersectResultStrict, ['invalid defined', '422'])) {

                return; // skip;
            }

            $data[] = [
                'projectableFields' => explode_sanitized($projectableFields),
                'definedFields' => explode_sanitized($definedFields),
                'clientInput' => $clientInput,
                'intersectResultStrict' => explode_sanitized($intersectResultStrict),
            ];
        });
    }

    public function exceptProjectionNonStrict()
    {
        $rowCounter = 1;

        $CELL_ROW_STARTS_AT = 3;
        $CELL_COLS_LENGTH = 11;

        return $this->readFileAndReturnData(function (&$data, $row) use (
            &$rowCounter,
            $CELL_ROW_STARTS_AT,
        ) {
            if ($rowCounter++ < $CELL_ROW_STARTS_AT) {
                return; // skip
            }

            if ($row[at('A')] == '') { // skip no value row
                return; //
            }

            $projectableFields = $row[at('A')];
            $definedFields = $row[at('B')];
            $clientInput = $row[at('C')];
            $exceptResultNonStrict = $row[at('D')];

            if ($exceptResultNonStrict === 'invalid defined') {
                return; // skip;
            }

            $data[] = [
                'projectableFields' => explode_sanitized($projectableFields),
                'definedFields' => explode_sanitized($definedFields),
                'clientInput' => $clientInput,
                'exceptResultNonStrict' => explode_sanitized($exceptResultNonStrict),
            ];
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

$csvFile = $argv[1];
var_dump($csvFile);
// echo (new CSVExporter($csvFile))->export();
