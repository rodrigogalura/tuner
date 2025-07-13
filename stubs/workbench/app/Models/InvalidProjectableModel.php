<?php

namespace Workbench\App\Models;

use Laradigs\Tweaker\CanTweak;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvalidProjectableModel extends Model
{
    /** @use HasFactory<\Database\Factories\InvalidProjectableModelFactory> */
    use HasFactory;
    use CanTweak;

    protected $fillable = [
        'name',
    ];

    protected function getProjectableFields()
    {
        return ['email'];
    }
}
