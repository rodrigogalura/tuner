<?php

namespace Laradigs\Tweaker\V31\TruthTable;

trait Exportable
{
    public function export($filepath, $data, ?callable $beforeWriteData = null, ?callable $afterWriteData = null)
    {
        $handle = fopen($filepath, 'w');

        if (! is_null($beforeWriteData)) {
            $beforeWriteData($handle);
        }

        foreach ($data as $d) {
            fputcsv($handle, $d);
        }

        fclose($handle);

        if (! is_null($afterWriteData)) {
            $afterWriteData();
        } else {
            if (file_exists($filepath)) {
                echo "CSV file created successfully: {$filepath}".PHP_EOL;
            }
        }
    }
}
