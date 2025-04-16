# ⚙️ Advanced Features

Take your API game further with these powerful features.

---

## 1. Custom Status Codes

You can customize status codes per response type:

```php
Api::error('Something went wrong.', 422);
```

## 2. Macroable Response Builder

You can register your own macros!

```php
Api::macro('customUnauthorized', function () {
    return Api::error('Unauthorized', 401);
});
```

Usage:

```php
return Api::customUnauthorized();
```

## 3. Structured Validation Errors

If you throw a Laravel validation error, Api Igniter formats it automatically:

```php
$request->validate([
    'email' => 'required|email',
]);
```

Output:

```php
{
  "status": "error",
  "message": "The email field is required.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

## 4. Configurable Defaults

Tweak the default keys, structure, and codes in config/api-igniter.php to match your needs.
