# 📦 Installation Guide

Follow these steps to install and set up **Tuner** in your Laravel project.

## Requirements

- PHP 8.2 or higher
- Laravel 10 or 11
- Composer

## Step 1: Install via Composer

```bash
composer require rodrigogalura/tuner
```

## Step 2: Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=tuner-config
```

This will publish a config/tuner.php file that you can customize.

## Step 3: Usage

You can now use the API response helpers in your controllers:

```php
use Tuner\Api;

return Api::success([
    'message' => 'Operation completed.',
    'data' => $user,
]);
```



