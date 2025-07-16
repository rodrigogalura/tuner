<?php

class CSVToArray
{
    private array $fileMethod = [
        'truth-table.csv' => 'projectionField',
        'searching-truth-table.csv' => 'searching',
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
                if ($row[0] !== '') { // skip no value row
                    $callback($data, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

    public function shortArrayExport()
    {
        $export = var_export($this->data, true);
        $export = preg_replace("/^(\s*)array\s*\(/m", '$1[', $export);
        $export = preg_replace("/\)(,?)$/m", ']$1', $export);

        return $export;
    }

    public function projectionField()
    {
        return $this->readFileAndReturnData(function (&$data, $row) {
            // convert 'empty' string to ''
            for ($i = 0; $i <= 5; $i++) {
                if ($row[$i] === 'empty') {
                    $row[$i] = '';
                }
            }

            // fields
            if (! is_numeric($row[4])) {
                $data[] = [
                    'projectableFields' => explode_sanitized($row[0]),
                    'definedFields' => explode_sanitized($row[1]),
                    'clientInput' => $row[2],
                    'expectedResult' => explode_sanitized($row[4]),
                ];
            }
        });

        // $data = [];
        // if (($handle = fopen($this->csvPath, 'r')) !== false) {
        //     while (($row = fgetcsv($handle, 0, $this->delimiter, $this->enclosure, $this->escape)) !== false) {
        //         if ($row[0] !== '') { // skip no value row

        //             // convert 'empty' string to ''
        //             for ($i = 0; $i <= 5; $i++) {
        //                 if ($row[$i] === 'empty') {
        //                     $row[$i] = '';
        //                 }
        //             }

        //             // fields
        //             if (! is_numeric($row[4])) {
        //                 $data[] = [
        //                     'projectableFields' => explode_sanitized($row[0]),
        //                     'definedFields' => explode_sanitized($row[1]),
        //                     'clientInput' => $row[2],
        //                     'expectedResult' => explode_sanitized($row[4]),
        //                 ];
        //             }

        //             // fields!
        //             // if (! is_numeric($row[5])) {
        //             //     $data[] = [
        //             //         'projectableFields' => explode_sanitized($row[0]),
        //             //         'definedFields' => explode_sanitized($row[1]),
        //             //         'clientInput' => $row[2],
        //             //         'expectedResult' => explode_sanitized($row[5]),
        //             //     ];
        //             // }
        //         }
        //     }
        //     fclose($handle);
        // }

        // return $data;
    }

    public function searching()
    {
        return $this->readFileAndReturnData(function (&$data, $row) {
            // convert 'empty' string to ''
            for ($i = 0; $i <= 5; $i++) {
                if ($row[$i] === 'empty') {
                    $row[$i] = '';
                }
            }

            // fields
            if (! is_numeric($result_fields = $row[7])) {
                $data[] = [
                    'searchableFields' => explode_sanitized($row[0]),

                    'search_fields' => $row[1],
                    'search_value_no_wildcard' => $row[2],
                    'search_value_both_wildcard' => $row[3],
                    'search_value_left_wildcard' => $row[4],
                    'search_value_right_wildcard' => $row[5],

                    'result_fields' => $result_fields,
                    'result_value_no_wildcard' => $row[8],
                    'result_value_both_wildcard' => $row[9],
                    'result_value_left_wildcard' => $row[10],
                    'result_value_right_wildcard' => $row[11],
                ];
            }
        });
    }
}

function explode_sanitized(string $str, string $delimiter = ',')
{
    return array_filter(array_map('trim', explode($delimiter, $str)));
}

// $csvToArray = new CSVToArray('truth-table.csv');
// echo $csvToArray->shortArrayExport();

$csvToArray = new CSVToArray('searching-truth-table.csv');
echo $csvToArray->shortArrayExport();

/*
    Run this script using the command:
    php truth-table-export-script.php | pbcopy
 */
