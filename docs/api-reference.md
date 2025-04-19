# 📘 API Reference

This section covers all public-facing methods provided by **API Igniter**.

---

## Api::success(array $data = [], int $status = 200)

Send a successful response.

```php
Api::success([
    'message' => 'User fetched successfully',
    'data' => $user,
]);
```

## Api::error(string $message, int $status = 400, array $errors = [])

Send a generic error response.

```php
Api::error('Something went wrong', 422);
```

## Api::withData(mixed $data)

Chain method for attaching custom data.

```php
Api::withData($user)->success();
```

## Api::macro(string $name, Closure $callback)

Define a custom macro.

```php
Api::macro('unauthorized', fn() => Api::error('Unauthorized', 401));
```

## config/api-igniter.php

You can configure:

- default_success_status
- default_error_status
- Key names like status, message, data, errors
