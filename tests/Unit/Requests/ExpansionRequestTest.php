<?php

// use Tuner\Columns\DefinedColumns;
use Illuminate\Database\Eloquent\Model;
use Tuner\Columns\ExpandableRelations;
use Tuner\Columns\FilterableColumns;
use Tuner\Columns\ProjectableColumns;
use Tuner\Columns\SearchableColumns;
use Tuner\Columns\SortableColumns;
use Tuner\Exceptions\TunerException;
use Tuner\Requests\ExpansionRequest;

beforeEach(function (): void {
    Mockery::globalHelpers();

    $this->config = [
        'projection' => [
            'key' => [
                'intersect' => env('TUNER_INTERSECT_KEY', 'columns'),
                'except' => env('TUNER_EXCEPT_KEY', 'columns!'),
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
});

describe('Expansion Request', function (): void {
    it('should thrown an exception when expandable relations are empty.', function (): void {
        // Prepare
        $request = [
            'expand' => [
                'phone' => 'p',
            ],
        ];

        $model = mock(new class extends Model {});

        // Act & Assert
        new ExpansionRequest($request, $this->config, $model, definedColumns: ['*'], expandableRelations: []);
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
        new ExpansionRequest($request, $this->config, $model, definedColumns: ['*'], expandableRelations: [
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

        $subjectModel = mock(new class extends Model {});
        $subjectModel->shouldReceive($relation)
            ->andReturn('foo');

        // Act & Assert
        new ExpansionRequest($request, $this->config, $subjectModel, definedColumns: ['*'], expandableRelations: [
            $relation => [
                'options' => ['invalid_option' => ['*']],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: ExpandableRelations::ERR_CODE_INVALID_OPTION
    );

    it('should thrown an exception when expansion projectable columns are empty.', function (): void {
        // Prepare
        $request = [
            'expand' => [
                'posts' => 'p',
            ],
            'p_columns' => '*',
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
        new ExpansionRequest($request, $this->config, $subjectModel, definedColumns: ['*'], expandableRelations: [
            $relation => [
                'options' => ['projectable_columns' => []],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: ProjectableColumns::ERR_CODE_DISABLED
    );

    it('should thrown an exception when expansion projectable columns are not in visible columns.', function (): void {
        // Prepare
        $request = [
            'expand' => [
                'posts' => 'p',
            ],
            'p_columns' => '*',
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
        new ExpansionRequest($request, $this->config, $subjectModel, definedColumns: ['*'], expandableRelations: [
            $relation => [
                'table' => $table,
                'options' => ['projectable_columns' => ['bar']],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: ProjectableColumns::ERR_CODE_PCOLS_VCOLS_NO_MATCH
    );

    it('should thrown an exception when expansion sortable columns are empty.', function (): void {
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
        new ExpansionRequest($request, $this->config, $subjectModel, definedColumns: ['*'], expandableRelations: [
            $relation => [
                'options' => ['sortable_columns' => []],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: SortableColumns::ERR_CODE_DISABLED
    );

    it('should thrown an exception when expansion sortable columns are not in visible columns.', function (): void {
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
        new ExpansionRequest($request, $this->config, $subjectModel, definedColumns: ['*'], expandableRelations: [
            $relation => [
                'table' => $table,
                'options' => ['sortable_columns' => ['bar']],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: SortableColumns::ERR_CODE_PCOLS_VCOLS_NO_MATCH
    );

    it('should thrown an exception when expansion searchable columns are empty.', function (): void {
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
        new ExpansionRequest($request, $this->config, $subjectModel, definedColumns: ['*'], expandableRelations: [
            $relation => [
                'options' => ['searchable_columns' => []],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: SearchableColumns::ERR_CODE_DISABLED
    );

    it('should thrown an exception when expansion searchable columns are not in visible columns.', function (): void {
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
        new ExpansionRequest($request, $this->config, $subjectModel, definedColumns: ['*'], expandableRelations: [
            $relation => [
                'table' => $table,
                'options' => ['searchable_columns' => ['bar']],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: SearchableColumns::ERR_CODE_PCOLS_VCOLS_NO_MATCH
    );

    it('should thrown an exception when expansion filterable columns are empty.', function (): void {
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
        new ExpansionRequest($request, $this->config, $subjectModel, definedColumns: ['*'], expandableRelations: [
            $relation => [
                'options' => ['filterable_columns' => []],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: FilterableColumns::ERR_CODE_DISABLED
    );

    it('should thrown an exception when expansion filterable columns are not in visible columns.', function (): void {
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
        new ExpansionRequest($request, $this->config, $subjectModel, definedColumns: ['*'], expandableRelations: [
            $relation => [
                'table' => $table,
                'options' => ['filterable_columns' => ['bar']],
            ],
        ]);
    })->throws(
        TunerException::class,
        exceptionCode: FilterableColumns::ERR_CODE_PCOLS_VCOLS_NO_MATCH
    );
});
