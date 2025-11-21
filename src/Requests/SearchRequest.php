<?php

namespace Tuner\Requests;

use Illuminate\Support\Str;
use Tuner\Exceptions\ClientException;
use Tuner\Fields\Fields;
use Tuner\Fields\SearchableFields;
use Tuner\Tuner;

/**
 * @internal
 */
class SearchRequest extends Request implements RequestInterface
{
    public function __construct(
        array $request,
        private array $config,
        private array $visibleFields,
        private array $searchableFields,
    ) {
        parent::__construct($request, $config[Tuner::PARAM_KEY]);
    }

    private static function searchKeywordInterpreter($searchRequest)
    {
        [$fields, $searchKeyword] = [key($searchRequest), current($searchRequest)];

        if (! str_starts_with($searchKeyword, '*') && ! str_ends_with($searchKeyword, '*')) {
            $searchKeyword = "*{$searchKeyword}*";
        }

        return [$fields => Str::replaceMatches(subject: $searchKeyword, pattern: '/^\*|\*$/', replace: '%')];
    }

    protected function validate()
    {
        $searchableFields = (new SearchableFields($this->searchableFields, $this->visibleFields))();

        // Validate search
        $searchRequest = current($this->request); // unwrap
        throw_unless(is_array($searchRequest), new ClientException('The ['.$this->key.'] must be array!'));
        throw_unless(count($searchRequest) === 1, new ClientException('The ['.$this->key.'] must be only one value!'));

        $fields = explode(',', key($searchRequest));

        // Validate fields
        $fields = new Fields($fields, $searchableFields);
        throw_if(empty($requestedFields = $fields->intersect()->implode()->get()), new ClientException('Invalid fields. It must be one of the following searchable fields: ['.implode(', ', $searchableFields).']'));

        $searchKeyword = current($searchRequest);

        // Validate values
        throw_if(strlen($searchKeyword) < $this->config['minimum_length'], new ClientException(sprintf('Keyword characters must be at least %d length.', $this->config['minimum_length'])));

        $this->request = [$this->key => static::searchKeywordInterpreter([$requestedFields => $searchKeyword])];
    }
}
