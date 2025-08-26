<?php

namespace Laradigs\Tweaker\TruthTableGenerator\Projection;

use Laradigs\Tweaker\Exceptions\Experiment\NoDefinedColumnsException;
use Laradigs\Tweaker\Exceptions\Experiment\ProjectionDisabledException;
use Laradigs\Tweaker\Exceptions\Experiment\SomeNotInProjectableColumnsException;
use Laradigs\Tweaker\Exceptions\Experiment\SomeNotInVisibleColumnsException;
use Laradigs\Tweaker\TruthTableGenerator\TruthTableGenerator;

class Projection extends TruthTableGenerator
{
    // public const PROJECTION_NAME = 'Projection';

    // public const PROJECTION_EXCEPT_NAME = 'Projection Except';

    public function __construct(string $file)
    {
        $appPath = dirname(dirname(__DIR__));

        parent::__construct("{$appPath}/truth-table/{$file}");
    }

    private function someNotInVisibleColumns(array $columns)
    {
        return $this->someArr1NotInArr2($columns, parent::VISIBLE_COLUMNS);
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

    // private function nonStrictIntersect(array $p, array $q, array $r)
    // {
    //     return array_intersect(array_intersect($p, $q), $r);
    // }

    // private function strictIntersect(array $nonStrictIntersect, array $r)
    // {
    //     return $this->someArr1NotInArr2(arr1: $r, arr2: $nonStrictIntersect)
    //             ? [parent::PLACEHOLDER_UNPROCESSABLE]
    //             : $nonStrictIntersect;
    // }

    // private function nonStrictExcept(array $p, array $q, array $r)
    // {
    //     // return array_intersect(array_intersect($p, $q), $r);
    // }

    // private function strictExcept(array $nonStrictExcept, array $r)
    // {
    //     // return $this->someArr1NotInArr2(arr1: $r, arr2: $nonStrictExcept)
    //     //         ? [parent::PLACEHOLDER_UNPROCESSABLE]
    //     //         : $nonStrictExcept;
    // }

    // public function intersectCSV()
    // {
    //     $p = $q = $r = [
    //         ['*'],
    //         ['id'],
    //         ['name'],
    //         ['id', 'name'],
    //         [],
    //     ];

    //     fputcsv($this->handle, ['Truth Table']);
    //     fputcsv($this->handle, [
    //         'Projectable (p)', 'Defined (q)', 'Client (r)',
    //         'Intersect - Non-strict',
    //         'Intersect - Strict',
    //     ]);

    //     foreach ($p as $i) {
    //         $pp = $i;
    //         $this->extractIfAsterisk($pp);

    //         foreach ($q as $j) {
    //             $qq = $j;
    //             $this->extractIfAsterisk($qq);

    //             foreach ($r as $k) {
    //                 $rr = $k;
    //                 $this->extractIfAsterisk($rr);

    //                 try {
    //                     $this->validate($pp, $qq);

    //                     $nonStrictIntersect = $this->nonStrictIntersect($pp, $qq, $rr);
    //                     $strictIntersect = $this->strictIntersect($nonStrictIntersect, $rr);
    //                 } catch (
    //                     ProjectionDisabledException|
    //                     NoDefinedColumnsException|
    //                     SomeNotInVisibleColumnsException|
    //                     SomeNotInProjectableColumnsException $e
    //                 ) {
    //                     $nonStrictIntersect =
    //                     $strictIntersect =
    //                     [$e->getCode()];
    //                 }

    //                 fputcsv($this->handle, array_map(fn ($columns) => empty($columns)
    //                         ? parent::PLACEHOLDER_EMPTY
    //                         : implode(', ', $columns),

    //                     [$i, $j, $k, $nonStrictIntersect, $strictIntersect]
    //                 ));
    //             }
    //             $this->skipRow();
    //         }
    //         $this->skipRow();
    //     }

    //     return $this;
    // }

    // public function exceptCSV()
    // {
    //     $p = $q = $r = [
    //         ['*'],
    //         ['id'],
    //         ['name'],
    //         ['id', 'name'],
    //         [],
    //     ];

    //     fputcsv($this->handle, ['Truth Table']);
    //     fputcsv($this->handle, [
    //         'Projectable (p)', 'Defined (q)', 'Client (r)',
    //         'Except - Non-strict',
    //         'Except - Strict',
    //     ]);

    //     foreach ($p as $i) {
    //         $pp = $i;
    //         $this->extractIfAsterisk($pp);

    //         foreach ($q as $j) {
    //             $qq = $j;
    //             $this->extractIfAsterisk($qq);

    //             foreach ($r as $k) {
    //                 $rr = $k;
    //                 $this->extractIfAsterisk($rr);

    //                 try {
    //                     $this->validate($pp, $qq);

    //                     $nonStrictExcept = $this->nonStrictExcept($pp, $qq, $rr);
    //                     $strictExcept = $this->strictExcept($nonStrictExcept, $rr);
    //                 } catch (
    //                     ProjectionDisabledException|
    //                     NoDefinedColumnsException|
    //                     SomeNotInVisibleColumnsException|
    //                     SomeNotInProjectableColumnsException $e
    //                 ) {
    //                     $nonStrictExcept =
    //                     $strictExcept =
    //                     [$e->getCode()];
    //                 }

    //                 // Cannot except all projectable columns

    //                 fputcsv($this->handle, array_map(fn ($columns) => empty($columns)
    //                         ? parent::PLACEHOLDER_EMPTY
    //                         : implode(', ', $columns),

    //                     [$i, $j, $k, $nonStrictExcept, $strictExcept]
    //                 ));
    //             }
    //             $this->skipRow();
    //         }
    //         $this->skipRow();
    //     }

    //     return $this;
    // }

    public function generate()
    {
        fclose($this->handle);

        if (file_exists($this->filename)) {
            echo "CSV file created successfully: $this->filename".PHP_EOL;
        }
    }
}
