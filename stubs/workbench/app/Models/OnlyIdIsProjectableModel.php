<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tuner\Tunable;

class OnlyIdIsProjectableModel extends Model
{
    /** @use HasFactory<\Database\Factories\OnlyIdIsProjectableModelFactory> */
    use HasFactory;

    use Tunable;

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function getProjectableColumns()
    {
        return ['id'];
    }

    protected function getSearchableColumns()
    {
        return ['id'];
    }

    protected function getSortableColumns()
    {
        return ['id'];
    }
}
