# 🔥 Api Igniter

**Api Igniter** is a Laravel package designed to power up your API development with clean, consistent, and customizable responses out of the box.

---

## 🚀 Features

- ✨ Plug-and-play setup for Laravel APIs
- 🧰 Built-in tools to simplify response handling, error formatting, and more
- 🧪 Tested with [Pest](https://pestphp.com/)
- ⚡ Designed for scalability and extensibility

---

## Installation

```
composer require rgalura/api-igniter
```

---

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

---

## Documentation

Coming soon. For now, explore the package in /src and check out example usage in /workbench.

---

## Contributing

Checkout the documentation for contributing [here](https://github.com/rodrigogalura/api-igniter/blob/main/CONTRIBUTING.md)

---

## Roadmap

---

## License

---

This package is open-sourced software licensed under the MIT license.
