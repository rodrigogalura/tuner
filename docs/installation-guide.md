# 📦 Installation Guide

Follow these steps to install and set up **API Igniter** in your Laravel project.

## Requirements

- PHP 8.2 or higher
- Laravel 10 or 11
- Composer

## Step 1: Install via Composer

```bash
composer require rgalura/api-igniter
```

## Step 2: Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=api-igniter-config
```

This will publish a config/api-igniter.php file that you can customize.

## Step 3: Usage

You can now use the API response helpers in your controllers:

```php
use ApiIgniter\Facades\Api;

return Api::success([
    'message' => 'Operation completed.',
    'data' => $user,
]);
```



