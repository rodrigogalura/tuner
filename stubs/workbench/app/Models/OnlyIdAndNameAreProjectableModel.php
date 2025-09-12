<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlyIdAndNameAreProjectableModel extends Model
{
    /** @use HasFactory<\Database\Factories\OnlyIdAndNameAreProjectableModelFactory> */
    use HasFactory;

    use \Tuner\V33\Tunable;

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function getProjectableFields()
    {
        return ['id', 'name'];
    }

    protected function getSearchableFields()
    {
        return ['id', 'name'];
    }

    protected function getSortableFields()
    {
        return ['id', 'name'];
    }
}
