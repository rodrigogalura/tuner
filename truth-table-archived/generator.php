<?php

class ProjectionCSV extends CSVGenerator
{
    private function extractIfAsterisk(array &$columns)
    {
        if ($columns === ['*']) {
            $columns = parent::VISIBLE_COLUMNS;
        }
    }

    private function someNotInVisibleColumns(array $columns)
    {
        return ! empty(array_diff($columns, parent::VISIBLE_COLUMNS));
    }

    private function someArr1NotInArr2(array $arr1, array $arr2)
    {
        return ! empty(array_diff($arr1, $arr2));
    }

    private function validate(array $p, array $q)
    {
        if (empty($p)) {
            throw new ProjectionDisabledException;
        }

        if (empty($q)) {
            throw new NoDefinedColumnsException;
        }

        if (
            $this->someNotInVisibleColumns($p) ||
            $this->someNotInVisibleColumns($q)
        ) {
            throw new SomeNotInVisibleColumnsException;
        }

        if ($this->someArr1NotInArr2(arr1: $q, arr2: $p)) {
            throw new SomeNotInProjectableColumnsException;
        }
    }

    private function nonStrictIntersect(array $p, array $q, array $r)
    {
        return array_intersect(array_intersect($p, $q), $r);
    }

    private function strictIntersect(array $nonStrictIntersect, array $r)
    {
        return $this->someArr1NotInArr2(arr1: $r, arr2: $nonStrictIntersect)
                ? [parent::PLACEHOLDER_UNPROCESSABLE]
                : $nonStrictIntersect;
    }

    public function intersect()
    {
        $p = $q = $r = [
            ['*'],
            ['id'],
            ['name'],
            ['id', 'name'],
            [],
        ];

        fputcsv($this->handle, ['Truth Table']);
        fputcsv($this->handle, [
            'Projectable (p)', 'Defined (q)', 'Client (r)',
            'Intersect - Non-strict',
            'Intersect - Strict',
        ]);

        foreach ($p as $i) {
            $pp = $i;
            $this->extractIfAsterisk($pp);

            foreach ($q as $j) {
                $qq = $j;
                $this->extractIfAsterisk($qq);

                foreach ($r as $k) {
                    $rr = $k;
                    $this->extractIfAsterisk($rr);

                    try {
                        $this->validate($pp, $qq);

                        $nonStrictIntersect = $this->nonStrictIntersect($pp, $qq, $rr);
                        $strictIntersect = $this->strictIntersect($nonStrictIntersect, $rr);
                    } catch (
                        ProjectionDisabledException|
                        NoDefinedColumnsException|
                        SomeNotInVisibleColumnsException|
                        SomeNotInProjectableColumnsException $e
                    ) {
                        $nonStrictIntersect =
                        $strictIntersect =
                        [$e->getCode()];
                    }

                    fputcsv($this->handle, array_map(fn ($columns) => empty($columns)
                            ? parent::PLACEHOLDER_EMPTY
                            : implode(', ', $columns),

                        [$i, $j, $k, $nonStrictIntersect, $strictIntersect]
                    ));
                }
                $this->skipRow();
            }
            $this->skipRow();
        }

        return $this;
    }

    public function generate()
    {
        fclose($this->handle);

        if (file_exists($this->filename)) {
            echo "CSV file created successfully: $this->filename".PHP_EOL;
        }
    }
}

abstract class CSVGenerator
{
    protected const VISIBLE_COLUMNS = ['id', 'name'];

    protected const PLACEHOLDER_EMPTY = '[EMPTY]';

    protected const PLACEHOLDER_UNPROCESSABLE = 422;

    protected $handle;

    public function __construct(protected $filename)
    {
        $this->handle = fopen($filename, 'w');
    }

    protected function skipRow()
    {
        fputcsv($this->handle, []);
    }

    // public function exceptProjectionNonStrict()
    // {
    //     $nonStrictExcept = function (string $p, string $q, string $r) {
    //         $this->extractIfAsteriskOtherwiseExplode($p, ALL_ITEMS);
    //         $this->extractIfAsteriskOtherwiseExplode($q, ALL_ITEMS);

    //         if (empty($pq = array_intersect($p, $q))) {
    //             return 'invalid defined';
    //         }

    //         $this->extractIfAsteriskOtherwiseExplode($r, $pq);

    //         return implode(', ', array_intersect($pq, $r));
    //     };

    //     $p = $q = $r = ['*', 'id', 'name', 'id, name'];

    //     fputcsv($this->handle, ['Truth Table']);
    //     fputcsv($this->handle, ['Projectable (p)', 'Defined (q)', 'Client (r)', 'Intersect - Non-strict']);

    //     foreach ($p as $i) {
    //         foreach ($q as $j) {
    //             foreach ($r as $k) {
    //                 fputcsv($this->handle, [$i, $j, $k, $nonStrictExcept($i, $j, $k)]);
    //             }
    //             $this->skipRow();
    //         }

    //         $this->skipRow();
    //     }

    //     return $this;
    // }

    // public function exceptProjectionStrict()
    // {
    //     $strictIntersect = function (string $p, string $q, string $r) {
    //         $this->extractIfAsteriskOtherwiseExplode($p, ALL_ITEMS);
    //         $this->extractIfAsteriskOtherwiseExplode($q, ALL_ITEMS);

    //         if (empty($pq = array_intersect($p, $q))) {
    //             return 'invalid defined';
    //         }

    //         $pq = array_intersect($p, $q);
    //         $this->extractIfAsteriskOtherwiseExplode($r, $pq);

    //         // client input not exist on projectable
    //         if (! empty(array_diff($r, $pq))) {
    //             return '422';
    //         }

    //         return implode(', ', array_intersect($pq, $r));
    //     };

    //     $p = $q = $r = ['*', 'id', 'name', 'id, name'];

    //     fputcsv($this->handle, ['Truth Table']);
    //     fputcsv($this->handle, ['Projectable (p)', 'Defined (q)', 'Client (r)', 'Intersect - Strict']);

    //     foreach ($p as $i) {
    //         foreach ($q as $j) {
    //             foreach ($r as $k) {
    //                 fputcsv($this->handle, [$i, $j, $k, $strictIntersect($i, $j, $k)]);
    //             }
    //             $this->skipRow();
    //         }

    //         $this->skipRow();
    //     }

    //     return $this;
    // }

    abstract public function generate();
}

class ProjectionDisabledException extends \Exception
{
    public function __construct(string $message = 'Projection is disabled', int $code = 1, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
class NoDefinedColumnsException extends \Exception
{
    public function __construct(string $message = 'No defined columns', int $code = 51, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
class SomeNotInVisibleColumnsException extends \Exception
{
    public function __construct(string $message = 'Not in visible columns', int $code = 52, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
class SomeNotInProjectableColumnsException extends \Exception
{
    public function __construct(string $message = 'Not in projectable columns', int $code = 52, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
class UnprocessableException extends \Exception
{
    public function __construct(string $message = 'Unprocessable Entity', int $code = 422, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
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

(new ProjectionCSV(__DIR__.'/intersect-projection.csv'))
    ->intersect()
    ->generate();

// (new CSVGenerator(__DIR__.'/except-projection.csv'))
//     ->exceptProjectionNonStrict()
//     ->close();
