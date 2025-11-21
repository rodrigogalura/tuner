<?php

// use Tuner\Fields\DefinedFields;
use Illuminate\Database\Eloquent\Model;
use Tuner\Exceptions\TunerException;
use Tuner\Fields\ExpandableRelations;
use Tuner\Fields\FilterableFields;
use Tuner\Fields\ProjectableFields;
use Tuner\Fields\SearchableFields;
use Tuner\Fields\SortableFields;
use Tuner\Requests\ExpansionRequest;

beforeEach(function (): void {
    Mockery::globalHelpers();

    $this->config = [
        'projection' => [
            'key' => [
                'intersect' => env('TUNER_INTERSECT_KEY', 'fields'),
                'except' => env('TUNER_EXCEPT_KEY', 'fields!'),
            ],
        ],

        'sort' => [
            'key' => env('TUNER_SORT_KEY', 'sort'),
        ],

        'search' => [
            'key' => env('TUNER_SEARCH_KEY', 'search'),
            'minimum_length' => env('TUNER_SEARCH_MINIMUM_LENGTH', 2),
        ],

        'filter' => [
            'key' => array_combine($keys = [
                env('TUNER_FILTER_KEY', 'filter'),
                env('TUNER_IN_KEY', 'in'),
                env('TUNER_BETWEEN_KEY', 'between'),
            ], $keys),
        ],

        'limit' => [
            'key' => array_combine($keys = [
                env('TUNER_LIMIT_KEY', 'limit'),
                env('TUNER_LIMIT_KEY', 'offset'),
            ], $keys),
        ],

        'pagination' => [
            'key' => env('TUNER_PAGINATION_KEY', 'page-size'),
        ],

        'expansion' => [
            'key' => env('TUNER_EXPANSION_KEY', 'expand'),
            'separator' => env('TUNER_EXPANSION_SEPARATOR', '_'),
        ],
    ];

    $this->model = mock(new class extends Model {});
});

describe('Expansion Request', function (): void {
    it('should thrown an exception when expandable relations are empty.', function (): void {
        // Prepare
        $request = [
            'expand' => [
                'phone' => 'p',
            ],
        ];

        // Act & Assert
        new ExpansionRequest($request, $this->config, $this->model, definedFields: ['*'], expandableRelations: []);
    })->throws(
        TunerException::class,
        exceptionCode: ExpandableRelations::ERR_CODE_DISABLED
    );

    it('should thrown an exception when expandable relations model are empty.', function (): void {
        // Prepare
        $request = [
            'expand' => [
                'phone' => 'p',
            ],
        ];

        $model = mock(new class extends Model {});

        // Act & Assert
        new ExpansionRequest($request, $this->config, $this->model, definedFields: ['*'], expandableRelations: [
            'notExistRelation' => [''],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: ExpandableRelations::ERR_CODE_INVALID_RELATION
    );

    it('should thrown an exception when expandable relations model options have invalid option.', function (): void {
        // Prepare
        $request = [
            'expand' => [
                'posts' => 'p',
            ],
        ];

        $relation = 'posts';

        $this->model->shouldReceive($relation)
            ->andReturn('foo');

        // Act & Assert
        new ExpansionRequest($request, $this->config, $this->model, definedFields: ['*'], expandableRelations: [
            $relation => [
                'options' => ['invalid_option' => ['*']],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: ExpandableRelations::ERR_CODE_INVALID_OPTION
    );

    it('should thrown an exception when expansion projectable fields are empty.', function (): void {
        // Prepare
        $request = [
            'expand' => [
                'posts' => 'p',
            ],
            'p_fields' => '*',
        ];

        $relation = 'posts';

        $subjectModel = new class extends Model
        {
            public function posts()
            {
                return $this->hasMany(new class extends Model {});
            }
        };

        // Act & Assert
        new ExpansionRequest($request, $this->config, $subjectModel, definedFields: ['*'], expandableRelations: [
            $relation => [
                'options' => ['projectable_fields' => []],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: ProjectableFields::ERR_CODE_DISABLED
    );

    it('should thrown an exception when expansion projectable fields are not in visible fields.', function (): void {
        // Prepare
        $request = [
            'expand' => [
                'posts' => 'p',
            ],
            'p_fields' => '*',
        ];

        $table = 'foo';
        $relation = 'posts';

        $subjectModel = new class extends Model
        {
            public function posts()
            {
                return $this->hasMany(new class extends Model {});
            }
        };

        Schema::shouldReceive('getColumnListing')
            ->with($table)
            ->andReturn(['id', 'name', 'email']);

        // Act & Assert
        new ExpansionRequest($request, $this->config, $subjectModel, definedFields: ['*'], expandableRelations: [
            $relation => [
                'table' => $table,
                'options' => ['projectable_fields' => ['bar']],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: ProjectableFields::ERR_CODE_PCOLS_VCOLS_NO_MATCH
    );

    it('should thrown an exception when expansion sortable fields are empty.', function (): void {
        // Prepare
        $request = [
            'expand' => [
                'posts' => 'p',
            ],
            'p_sort' => ['id' => 'desc'],
        ];

        $relation = 'posts';

        $subjectModel = new class extends Model
        {
            public function posts()
            {
                return $this->hasMany(new class extends Model {});
            }
        };

        // Act & Assert
        new ExpansionRequest($request, $this->config, $subjectModel, definedFields: ['*'], expandableRelations: [
            $relation => [
                'options' => ['sortable_fields' => []],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: SortableFields::ERR_CODE_DISABLED
    );

    it('should thrown an exception when expansion sortable fields are not in visible fields.', function (): void {
        // Prepare
        $request = [
            'expand' => [
                'posts' => 'p',
            ],
            'p_sort' => ['id' => 'desc'],
        ];

        $table = 'foo';
        $relation = 'posts';

        $subjectModel = new class extends Model
        {
            public function posts()
            {
                return $this->hasMany(new class extends Model {});
            }
        };

        Schema::shouldReceive('getColumnListing')
            ->with($table)
            ->andReturn(['id', 'name', 'email']);

        // Act & Assert
        new ExpansionRequest($request, $this->config, $subjectModel, definedFields: ['*'], expandableRelations: [
            $relation => [
                'table' => $table,
                'options' => ['sortable_fields' => ['bar']],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: SortableFields::ERR_CODE_PCOLS_VCOLS_NO_MATCH
    );

    it('should thrown an exception when expansion searchable fields are empty.', function (): void {
        // Prepare
        $request = [
            'expand' => [
                'posts' => 'p',
            ],
            'p_search' => ['name' => '*foo*'],
        ];

        $relation = 'posts';

        $subjectModel = new class extends Model
        {
            public function posts()
            {
                return $this->hasMany(new class extends Model {});
            }
        };

        // Act & Assert
        new ExpansionRequest($request, $this->config, $subjectModel, definedFields: ['*'], expandableRelations: [
            $relation => [
                'options' => ['searchable_fields' => []],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: SearchableFields::ERR_CODE_DISABLED
    );

    it('should thrown an exception when expansion searchable fields are not in visible fields.', function (): void {
        // Prepare
        $request = [
            'expand' => [
                'posts' => 'p',
            ],
            'p_search' => ['name' => '*foo*'],
        ];

        $table = 'foo';
        $relation = 'posts';

        $subjectModel = new class extends Model
        {
            public function posts()
            {
                return $this->hasMany(new class extends Model {});
            }
        };

        Schema::shouldReceive('getColumnListing')
            ->with($table)
            ->andReturn(['id', 'name', 'email']);

        // Act & Assert
        new ExpansionRequest($request, $this->config, $subjectModel, definedFields: ['*'], expandableRelations: [
            $relation => [
                'table' => $table,
                'options' => ['searchable_fields' => ['bar']],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: SearchableFields::ERR_CODE_PCOLS_VCOLS_NO_MATCH
    );

    it('should thrown an exception when expansion filterable fields are empty.', function (): void {
        // Prepare
        $request = [
            'expand' => [
                'posts' => 'p',
            ],
            'p_filter' => ['name' => 'foo'],
        ];

        $relation = 'posts';

        $subjectModel = new class extends Model
        {
            public function posts()
            {
                return $this->hasMany(new class extends Model {});
            }
        };

        // Act & Assert
        new ExpansionRequest($request, $this->config, $subjectModel, definedFields: ['*'], expandableRelations: [
            $relation => [
                'options' => ['filterable_fields' => []],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: FilterableFields::ERR_CODE_DISABLED
    );

    it('should thrown an exception when expansion filterable fields are not in visible fields.', function (): void {
        // Prepare
        $request = [
            'expand' => [
                'posts' => 'p',
            ],
            'p_filter' => ['name' => 'foo'],
        ];

        $table = 'foo';
        $relation = 'posts';

        $subjectModel = new class extends Model
        {
            public function posts()
            {
                return $this->hasMany(new class extends Model {});
            }
        };

        Schema::shouldReceive('getColumnListing')
            ->with($table)
            ->andReturn(['id', 'name', 'email']);

        // Act & Assert
        new ExpansionRequest($request, $this->config, $subjectModel, definedFields: ['*'], expandableRelations: [
            $relation => [
                'table' => $table,
                'options' => ['filterable_fields' => ['bar']],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: FilterableFields::ERR_CODE_PCOLS_VCOLS_NO_MATCH
    );
});
