<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tuner\Tunable;

class OnlyIdAndNameAreProjectableModel extends Model
{
    /** @use HasFactory<\Database\Factories\OnlyIdAndNameAreProjectableModelFactory> */
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
        return ['id', 'name'];
    }

    protected function getSearchableColumns()
    {
        return ['id', 'name'];
    }

    protected function getSortableColumns()
    {
        return ['id', 'name'];
    }
}
