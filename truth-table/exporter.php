<?php

// class CSVExporter
// {
//     // private array $fileMethod = [
//     //     'projection-intersect.csv' => [
//     //         'non-strict' => 'projectionIntersectNonStrict',
//     //         'strict' => 'projectionIntersectStrict',
//     //     ],
//     // ];

//     private array $fileMethod = [
//         'projection.csv' => [
//             'non-strict' => 'projectionIntersectNonStrict',
//             'strict' => 'projectionIntersectStrict',
//         ],
//     ];

//     private readonly array $data;

//     public function __construct(
//         private $csvPath,
//         private $delimiter = ',',
//         private $enclosure = '"',
//         private $escape = '\\'
//     ) {
//         if (! file_exists($this->csvPath) || ! is_readable($this->csvPath)) {
//             throw new Exception('CSV file not found or not readable.');
//         }

//         $csvFile = basename($csvPath);

//         if (! in_array($csvFile, array_keys($this->fileMethod))) {
//             throw new Exception('Invalid csv file provided.');
//         }

//         $method = $this->fileMethod[$csvFile];

//         $this->data = $this->{$method}();
//     }

//     private function readFileAndReturnData(callable $callback)
//     {
//         $data = [];
//         if (($handle = fopen($this->csvPath, 'r')) !== false) {
//             while (($row = fgetcsv($handle, 0, $this->delimiter, $this->enclosure, $this->escape)) !== false) {
//                 $callback($data, $row);
//             }
//             fclose($handle);
//         }

//         return $data;
//     }

//     public function export()
//     {
//         $export = var_export($this->data, true);
//         $export = preg_replace("/^(\s*)array\s*\(/m", '$1[', $export);
//         $export = preg_replace("/\)(,?)$/m", ']$1', $export);

//         return $export;
//     }

//     public function projection()
//     {
//         $rowCounter = 1;

//         $CELL_ROW_STARTS_AT = 3;
//         // $CELL_COLS_LENGTH = 7;

//         return $this->readFileAndReturnData(function (&$data, $row) use (
//             &$rowCounter,
//             $CELL_ROW_STARTS_AT,
//         ) {
//             if ($rowCounter++ < $CELL_ROW_STARTS_AT) {
//                 return; // skip
//             }

//             // if ($row[at('A')] == '') { // skip no value row
//             //     return; //
//             // }

//             $projectableFields = $row[at('A')];
//             $definedFields = $row[at('B')];
//             $clientInput = $row[at('C')];
//             $intersectResultNonStrict = $row[at('D')];
//             $intersectResultStrict = $row[at('E')];
//             $exceptResultNonStrict = $row[at('F')];
//             $exceptResultStrict = $row[at('G')];

//             if ($intersectResultNonStrict === 'invalid defined') {
//                 return; // skip;
//             }

//             $data[] = [
//                 'projectableFields' => explode_sanitized($projectableFields),
//                 'definedFields' => explode_sanitized($definedFields),
//                 'clientInput' => $clientInput,
//                 'intersectResultNonStrict' => explode_sanitized($intersectResultNonStrict),
//             ];
//         });
//     }

//     public function projectionIntersectNonStrict()
//     {
//         $rowCounter = 1;

//         $CELL_ROW_STARTS_AT = 3;
//         $CELL_COLS_LENGTH = 5;

//         return $this->readFileAndReturnData(function (&$data, $row) use (
//             &$rowCounter,
//             $CELL_ROW_STARTS_AT,
//         ) {
//             if ($rowCounter++ < $CELL_ROW_STARTS_AT) {
//                 return; // skip
//             }

//             if ($row[at('A')] == '') { // skip no value row
//                 return; //
//             }

//             $projectableFields = $row[at('A')];
//             $definedFields = $row[at('B')];
//             $clientInput = $row[at('C')];
//             $intersectResultNonStrict = $row[at('D')];

//             if ($intersectResultNonStrict === 'invalid defined') {
//                 return; // skip;
//             }

//             $data[] = [
//                 'projectableFields' => explode_sanitized($projectableFields),
//                 'definedFields' => explode_sanitized($definedFields),
//                 'clientInput' => $clientInput,
//                 'intersectResultNonStrict' => explode_sanitized($intersectResultNonStrict),
//             ];
//         });
//     }

//     public function projectionIntersectStrict()
//     {
//         $rowCounter = 1;

//         $CELL_ROW_STARTS_AT = 3;
//         $CELL_COLS_LENGTH = 5;

//         return $this->readFileAndReturnData(function (&$data, $row) use (
//             &$rowCounter,
//             $CELL_ROW_STARTS_AT,
//         ) {
//             if ($rowCounter++ < $CELL_ROW_STARTS_AT) {
//                 return; // skip
//             }

//             if ($row[at('A')] == '') { // skip no value row
//                 return; //
//             }

//             $projectableFields = $row[at('A')];
//             $definedFields = $row[at('B')];
//             $clientInput = $row[at('C')];
//             $intersectResultStrict = $row[at('E')];

//             if ($intersectResultStrict === 'invalid defined') {
//                 return; // skip;
//             }

//             $data[] = [
//                 'projectableFields' => explode_sanitized($projectableFields),
//                 'definedFields' => explode_sanitized($definedFields),
//                 'clientInput' => $clientInput,
//                 'intersectResultStrict' => explode_sanitized($intersectResultStrict),
//             ];
//         });
//     }
// }

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

class Exporter
{
    public function __construct(
        private $csvFile,
        private $delimiter = ',',
        private $enclosure = '"',
        private $escape = '\\'
    ) {
        if (! file_exists($this->csvFile) || ! is_readable($this->csvFile)) {
            throw new Exception('CSV file not found or not readable.');
        }
    }

    private function readFileAndReturnData(callable $callback)
    {
        $data = [];
        if (($handle = fopen($this->csvFile, 'r')) !== false) {
            while (($row = fgetcsv($handle, 0, $this->delimiter, $this->enclosure, $this->escape)) !== false) {
                $callback($data, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    private function exportHelper($data)
    {
        $export = var_export($data, true);
        $export = preg_replace("/^(\s*)array\s*\(/m", '$1[', $export);
        $export = preg_replace("/\)(,?)$/m", ']$1', $export);

        return $export;
    }

    public function exportProjection()
    {
        $rowCounter = 1;

        $CELL_ROW_STARTS_AT = 3;
        $CELL_COLS_LENGTH = 7;

        $dataArr = $this->readFileAndReturnData(function (&$data, $row) use (
            &$rowCounter,
            $CELL_ROW_STARTS_AT,
        ) {
            if ($rowCounter++ < $CELL_ROW_STARTS_AT) {
                return; // skip
            }

            $data[] = [
                'projectableFields' => explode_sanitized($row[at('A')]),
                'definedFields' => explode_sanitized($row[at('B')]),
                'clientInput' => $row[at('C')],
                'intersectResultNonStrict' => is_numeric($row[at('D')]) ? $row[at('D')] : explode_sanitized($row[at('D')]),
                'intersectResultStrict' => is_numeric($row[at('E')]) ? $row[at('E')] : explode_sanitized($row[at('E')]),
                'exceptResultNonStrict' => is_numeric($row[at('F')]) ? $row[at('F')] : explode_sanitized($row[at('F')]),
                'exceptResultStrict' => is_numeric($row[at('G')]) ? $row[at('G')] : explode_sanitized($row[at('G')]),
            ];
        });

        return $this->exportHelper($dataArr);
    }
}


$csvFile = $argv[1];

$exporter = new Exporter(__DIR__ . "/{$csvFile}");
echo $exporter->exportProjection();
