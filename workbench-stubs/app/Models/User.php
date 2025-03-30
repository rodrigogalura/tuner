<?php

namespace Workbench\App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use RGalura\ApiIgniter\Traits\BetweenFilterable;
use RGalura\ApiIgniter\Traits\Core\ApiIgniter;
use RGalura\ApiIgniter\Traits\Expandable;
use RGalura\ApiIgniter\Traits\Filterable;
use RGalura\ApiIgniter\Traits\InFilterable;
use RGalura\ApiIgniter\Traits\Projectable;
use RGalura\ApiIgniter\Traits\Searchable;
use RGalura\ApiIgniter\Traits\Sortable;
use Workbench\Database\Factories\UserFactory;

class User extends Authenticatable
{
    use ApiIgniter, BetweenFilterable, Expandable, Filterable, InFilterable, Projectable, Searchable, Sortable;
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
