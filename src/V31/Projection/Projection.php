<?php

namespace Laradigs\Tweaker\V31\Projection;

use Laradigs\Tweaker\V31\TruthTable\TruthTable;

class Projection
{
    private TruthTable $truthTable;

    public function __construct(array $columns)
    {
        $this->truthTable = new TruthTable($columns);
    }

    public function exportToCSV($filename, array $variables)
    {
        $handle = fopen($filename, 'w');

        fputcsv($handle, ['Truth Table']);
        fputcsv($handle, array_merge(
            array_keys($variables),
            ['Intersect', 'Intersect Strict', 'Except', 'Except Strict']
        ));

        $matrix = $this->matrix2d($variables);

        foreach ($matrix as $m) {
            if (isset($current) && $current !== $m[0]) {
                fputcsv($handle, []);
            }

            $current = $m[0];
            fputcsv($handle, $m);
        }

        fclose($handle);
        if (file_exists($filename)) {
            echo "CSV file created successfully: {$filename}".PHP_EOL;
        }
    }
}
