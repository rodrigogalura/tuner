<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tuner\Tunable;

class InvalidProjectableModel extends Model
{
    /** @use HasFactory<\Database\Factories\InvalidProjectableModelFactory> */
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
        return ['email'];
    }

    protected function getSearchableColumns()
    {
        return ['email'];
    }

    protected function getSortableColumns()
    {
        return ['email'];
    }
}
