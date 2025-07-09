<?php

use Illuminate\Database\Eloquent\Model;
use function RGalura\ApiIgniter\filter_explode;
use RGalura\ApiIgniter\Exceptions\InvalidFieldsException;

class Projection
{
    private readonly array $projectedFields;

    private string $clientInputKey = 'fields';
    private string $clientInputNotKey = 'fields!';

    public function __construct(
        private Model $model,
        private array $projectableFields,
        private array $definedFields,
        private array $clientInput,
    )
    {
        //
    }

    public function setClientInputFieldsKey($key)
    {
        $this->clientInputKey = $key;
    }

    public function setClientInputFieldsNotKey($key)
    {
        $this->clientInputNotKey = $key;
    }

    private function convertToValuesIfAsterisk(&$var)
    {
        if ($var === ['*']) {
            $var = $this->visibleFields();
        }
    }

    public function visibleFields()
    {
        return $this->model->getConnection()->getSchemaBuilder()->getColumnListing($this->model->getTable());
    }

    private function throwIfInvalidFields(array $fields)
    {
        if (! empty($diff = array_diff($fields, $this->visibleFields()))) {
            throw new InvalidFieldsException($diff, 1);
        }
    }

    public function handle()
    {
        if (empty($this->projectableFields)) {
            return;
        }

        $include = $this->clientInput[$this->clientInputKey] ?? null;
        $exclude = $this->clientInput[$this->clientInputNotKey] ?? null;

        if (isset($include, $exclude)) {
            return;
        }

        $this->convertToValuesIfAsterisk($this->projectableFields);
        $this->throwIfInvalidFields($this->projectableFields);

        $this->convertToValuesIfAsterisk($this->definedFields);
        $this->throwIfInvalidFields($this->definedFields);

        if (empty($this->projectableFields = array_values(array_intersect($this->projectableFields, $this->definedFields)))) {
            return;
        }

        $includeFn = function (array $include) {
            return $include === ['*']
                ? $this->projectableFields
                : array_values(array_intersect($this->projectableFields, $include));
        };

        $excludeFn = function (array $exclude) {
            return $exclude === ['*']
                ? []
                : array_values(array_diff($this->projectableFields, $exclude));
        };

        $this->projectedFields = match (true) {
            isset($include) => $includeFn(filter_explode($include)),
            isset($exclude) => $excludeFn(filter_explode($exclude)),
            default => null
        };

        return $this;
    }

    public function getProjectedFields()
    {
        return $this->projectedFields;
    }
}

beforeEach(function() {
    Mockery::globalHelpers();

    $table = 'users';
    $this->visibleFields = ['id', 'name'];

    $this->model = mock(Model::class);
    $this->model
        ->shouldReceive('getTable')
        ->andReturn($table)
        ->shouldReceive('getConnection->getSchemaBuilder->getColumnListing')
        ->with(Mockery::type('string'))
        ->andReturn($this->visibleFields)
        ;
});

afterEach(function() {
    Mockery::close();
});

it('should throw an exception if one of projectable fields is invalid', function () {
    // Prepare
    $notInVisibleFields = ['email'];
    $projection = new Projection(
        $this->model,
        projectableFields: $notInVisibleFields,
        definedFields: [],
        clientInput: [],
    );

    // Act & Assert
    expect(fn () => $projection->handle())->toThrow(InvalidFieldsException::class);
});

it('should throw an exception if one of defined fields is invalid', function () {
    // Prepare
    $notInVisibleFields = ['email'];
    $projection = new Projection(
        $this->model,
        projectableFields: ['id'],
        definedFields: $notInVisibleFields,
        clientInput: [],
    );

    // Act & Assert
    expect(fn () => $projection->handle())->toThrow(InvalidFieldsException::class);
});

it('should not perform any action if the projectable field\'s value is empty', function () {
    // Prepare
    $projection = new Projection(
        $this->model,
        projectableFields: [],
        definedFields: [],
        clientInput: [],
    );

    // Act & Assert
    expect($projection->handle())->toBeNull();
});

it('should not perform any action if the fields and fields! used at the same time', function () {
    // Prepare
    $projection = new Projection(
        $this->model,
        projectableFields: $this->visibleFields,
        definedFields: ['*'],
        clientInput: ['fields' => '*', 'fields!' => '*'],
    );

    // Act & Assert
    expect($projection->handle())->toBeNull();
});

it('should not perform any action if the projectable fields and defined fields are not intersect', function () {
    // Prepare
    $projection = new Projection(
        $this->model,
        projectableFields: [$this->visibleFields[0]],
        definedFields: [$this->visibleFields[1]],
        clientInput: [],
    );

    // Act & Assert
    expect($projection->handle())->toBeNull();
});

it('should passed all valid scenarios', function ($projectableFields, $definedFields, $clientInput, $expectedResult) {
    // Prepare
    $projection = new Projection(
        $this->model,
        $projectableFields,
        $definedFields,
        ['fields' => $clientInput],
    );

    $projection2 = new Projection(
        $this->model,
        $projectableFields,
        $definedFields,
        ['fields!' => $clientInput],
    );

    // Act & Assert
    expect($projection->handle()->getProjectedFields())->toBe($expectedResult['fields']);
    expect($projection2->handle()->getProjectedFields())->toBe($expectedResult['fields!']);
})
->with('truth-table');
