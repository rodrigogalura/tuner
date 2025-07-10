<?php

function explode_sanitized(string $str, string $delimiter = ',')
{
    return array_filter(array_map('trim', explode($delimiter, $str)));
}

function csv_to_array($csvPath, $delimiter = ',', $enclosure = '"', $escape = "\\") {
    if (!file_exists($csvPath) || !is_readable($csvPath)) {
        throw new Exception("CSV file not found or not readable.");
    }

    $data = [];
    if (($handle = fopen($csvPath, 'r')) !== false) {
        while (($row = fgetcsv($handle, 0, $delimiter, $enclosure, $escape)) !== false) {
            if ($row[0] !== '') { // skip no value row

                // convert 'empty' string to ''
                for ($i = 0; $i <= 5; ++$i) {
                    if ($row[$i] === 'empty') {
                        $row[$i] = '';
                    }
                }

                $data[] = [
                    'projectableFields' => explode_sanitized($row[0]),
                    'definedFields' => explode_sanitized($row[1]),
                    'clientInput' => $row[2],
                    'expectedResult' => [
                        'fields' => is_numeric($row[4]) ? null : explode_sanitized($row[4]),
                        'fields!' => is_numeric($row[5]) ? null : explode_sanitized($row[5])
                    ],
                ];
            }
        }
        fclose($handle);
    }

    return $data;
}

function short_array_export($value) {
    $export = var_export($value, true);
    $export = preg_replace("/^(\s*)array\s*\(/m", '$1[', $export);
    $export = preg_replace("/\)(,?)$/m", ']$1', $export);
    return $export;
}

$data = csv_to_array('truth-table.csv');

echo short_array_export($data);

/*
    Run this script using the command:
    php truth-table-export-script.php | pbcopy
 */
