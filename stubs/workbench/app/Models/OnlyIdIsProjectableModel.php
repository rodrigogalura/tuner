<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlyIdIsProjectableModel extends Model
{
    /** @use HasFactory<\Database\Factories\OnlyIdIsProjectableModelFactory> */
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
        return ['id'];
    }

    protected function getSearchableFields()
    {
        return ['id'];
    }

    protected function getSortableFields()
    {
        return ['id'];
    }
}
