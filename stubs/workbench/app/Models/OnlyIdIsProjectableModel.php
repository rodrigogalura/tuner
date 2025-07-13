<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laradigs\Tweaker\CanTweak;

class OnlyIdIsProjectableModel extends Model
{
    use CanTweak;

    /** @use HasFactory<\Database\Factories\OnlyIdIsProjectableModelFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected function getProjectableFields()
    {
        return ['id'];
    }
}
