# API Igniter

## Features

⚡ Elegant success & error response formatting
📦 Smart pagination helper
🧱 Built-in response macros
🧪 Pest-ready with test helpers
⚙️ Fully customizable

## Installation

```
composer require rgalura/api-igniter
```

## Basic Usage

1. Include `ApiIgniter` and chosen feature do you want to use in model. Example is `Projectable`

```php
use RGalura\ApiIgniter\Traits\Projectable;
use RGalura\ApiIgniter\Traits\Core\ApiIgniter;

class User extends Authenticatable
{
    use ApiIgniter, Projectable;
}
...
```

2. Use the scope method `send` inside your controller

```php
use App\Models\User;

class UserController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return User::send();
    }
}
```

## Documentation

Coming soon. For now, explore the package in /src and check out example usage in /workbench.

## Contributing

Pull requests are welcome. If you find an issue or have a feature request, open a GitHub issue and let’s chat.

## Roadmap

## License

MIT © rgalura
