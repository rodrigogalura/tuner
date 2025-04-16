# ❓ Frequently Asked Questions

---

## Why use Api Igniter instead of writing responses manually?

Api Igniter removes repetition and ensures consistent, clean API responses across your application.

---

## Is it compatible with Laravel 12?

Laravel 12 support is planned and tested. Keep an eye on the [CHANGELOG](../CHANGELOG.md) for updates.

---

## Can I customize the default keys (`message`, `data`, `errors`, etc.)?

Yes! Run the publish command and edit `config/api-igniter.php`.

---

## Does it work with API Resources?

Yes, wrap the resource in your `Api::success()` call.

```php
return Api::success([
    'data' => new UserResource($user),
]);
```

## What happens on validation errors?

Api Igniter auto-formats them into a clean JSON structure. You don’t need to handle it manually.

---
