<?php

class CSVGenerator
{
    private $handle;

    public function __construct(private $filename)
    {
        $this->handle = fopen($filename, 'w');
    }

    private function extractIfAsteriskOtherwiseExplode(string &$val, array $allItems)
    {
        $val = $val === '*'
            ? $allItems
            : array_filter(array_map('trim', explode(',', $val)));
    }

    private function skipRow()
    {
        fputcsv($this->handle, []);
    }

    public function intersectProjection()
    {
        define('ALL_ITEMS', ['id', 'name']);

        $intersect = function (string $p, string $q, string $r) {
            $this->extractIfAsteriskOtherwiseExplode($p, ALL_ITEMS);
            $this->extractIfAsteriskOtherwiseExplode($q, ALL_ITEMS);

            if (empty($pq = array_intersect($p, $q))) {
                return 'invalid defined';
            }

            $this->extractIfAsteriskOtherwiseExplode($r, $pq);

            return implode(', ', array_intersect($pq, $r));
        };

        $strictIntersect = function (string $p, string $q, string $r, string $intersectResult) {
            if (empty($intersectResult)) {
                return '422';
            }

            $this->extractIfAsteriskOtherwiseExplode($p, ALL_ITEMS);
            $this->extractIfAsteriskOtherwiseExplode($q, ALL_ITEMS);

            $pq = array_intersect($p, $q);
            $this->extractIfAsteriskOtherwiseExplode($r, $pq);

            if (! empty(array_diff($r, $pq))) {
                return '422';
            }

            return $intersectResult;
        };

        $p = $q = $r = ['*', 'id', 'name', 'id, name'];

        // temporary
        // $q = ['*'];

        fputcsv($this->handle, ['Truth Table']);
        fputcsv($this->handle, ['Projectable (p)', 'Defined (q)', 'Client (r)', 'Intersect - Non-strict', 'Intersect - Strict']);

        foreach ($p as $i) {
            foreach ($q as $j) {
                foreach ($r as $k) {
                    $nonStrict = $intersect($i, $j, $k);

                    fputcsv($this->handle, [
                        $i, $j, $k,
                        $nonStrict,
                        $strictIntersect($i, $j, $k, $nonStrict),
                    ]);
                }
                $this->skipRow();
            }

            $this->skipRow();
        }

        return $this;
    }

    public function close()
    {
        fclose($this->handle);

        if (file_exists($this->filename)) {
            echo "CSV file created successfully: $this->filename";
        }
    }
}

function dd($val)
{
    var_dump($val);
    exit;
}

function explode_sanitize(string $string, string $delimiter = ',')
{
    return array_filter(array_map('trim', explode($delimiter, $string)));
}

(new CSVGenerator(__DIR__.'/intersect-projection.csv'))
    ->intersectProjection()
    ->close();
