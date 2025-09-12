<?php

namespace Tuner\V33\ValueObjects\Requests;

use Exception;
use Illuminate\Support\Str;
use Tuner\V33\Tuner;
use Tuner\V33\ValueObjects\Columns;
use Tuner\V33\ValueObjects\SearchableColumns;

class SearchRequest extends Request implements RequestInterface
{
    public function __construct(
        private array $config,
        array $request,
        private array $visibleColumns,
        private array $searchableColumns,
    ) {
        parent::__construct($config[Tuner::PARAM_KEY], $request);
    }

    private static function searchKeywordInterpreter($searchRequest)
    {
        [$columns, $searchKeyword] = [key($searchRequest), current($searchRequest)];

        if (! str_starts_with($searchKeyword, '*') && ! str_ends_with($searchKeyword, '*')) {
            $searchKeyword = "*{$searchKeyword}*";
        }

        return [$columns => Str::of($searchKeyword)->replaceMatches('/^\*|\*$/', '%')->value];
    }

    protected function validate()
    {
        $searchableColumns = (new SearchableColumns($this->searchableColumns, $this->visibleColumns))();

        // Validate search
        $searchRequest = current($this->request); // unwrap
        throw_unless(is_array($searchRequest), new Exception('The ['.$this->key.'] must be array!'));
        throw_unless(count($searchRequest) === 1, new Exception('The ['.$this->key.'] must be only one value!'));

        $columns = explode(',', key($searchRequest));

        // Validate columns
        $columns = new Columns($columns, $searchableColumns);
        throw_if(empty($requestedColumns = $columns->intersect()->implode()->get()), new Exception('Invalid columns. It must be one of the following searchable columns: ['.implode(', ', $searchableColumns).']'));

        $searchKeyword = current($searchRequest);

        // Validate values
        throw_if(strlen($searchKeyword) < $this->config['minimum_length'], new Exception(sprintf('Keyword characters must be at least %d length.', $this->config['minimum_length'])));

        $this->request = [$this->key => static::searchKeywordInterpreter([$requestedColumns => $searchKeyword])];
    }
}
