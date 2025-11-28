<?php

namespace Tuner\Requests;

use Tuner\Exceptions\ClientException;
use Tuner\Fields\DefinedFields;
use Tuner\Fields\Fields;
use Tuner\Fields\ProjectableFields;
use Tuner\Tuner;

/**
 * @internal
 */
class ProjectionRequest extends Request implements RequestInterface
{
    public function __construct(
        array $request,
        array $config,
        private array $visibleFields,
        private array $projectableFields,
        private array $definedFields,
    ) {
        parent::__construct($request, $config[Tuner::PARAM_KEY]);
    }

    protected function validate()
    {
        switch (count($this->request)) {
            case 1:
                $p = (new ProjectableFields($this->projectableFields, $this->visibleFields))();
                $q = (new DefinedFields($this->definedFields, $this->visibleFields))();
                $projectableFields = array_intersect($p, $q);

                // Validate projection
                [$paramKey, $paramValue] = [key($this->request), current($this->request)];
                throw_unless(is_string($paramValue), new ClientException('The ['.$paramKey.'] must be string'));

                $projector = array_search($paramKey, $this->key);

                // Validate fields
                $fields = new Fields(explode(',', $paramValue), $projectableFields);
                throw_if(empty($projectedFields = $fields->{$projector}()->get()), new ClientException('The ['.$paramKey.'] must be use any of these projectable fields: ['.implode(', ', $projectableFields).']'));

                $this->request = [$paramKey => $projectedFields];

                break;

            case 2:
                $projectionModifiers = implode(', ', array_keys($this->request));
                throw new ClientException('Cannot use '.$projectionModifiers.' at the same time.');
            default:
                throw new ClientException('Number of projection key is invalid.');
        }
    }
}
