<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RGalura\ApiIgniter\ApiIgniter;
use RGalura\ApiIgniter\Expandable;
use RGalura\ApiIgniter\Projectable;

class Phone extends Model
{
    use ApiIgniter, Expandable, Projectable;

    /** @use HasFactory<\Database\Factories\PhoneFactory> */
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
