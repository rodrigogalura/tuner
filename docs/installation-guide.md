---
title: Installation Guide
level: 1
order: 1
---

# Installation Guide

Follow these 3 easy steps to install and set up **Tuner** in your Laravel project.

## Requirements

- PHP ^8.0
- Laravel ^12.0

---
<br>

## Step 1: Install via Composer

```bash
composer require rodrigogalura/tuner
```

<br>

## Step 2: Setup

Use the <ins>Tunable</ins> trait:

```php
...

class User extends Authenticatable
{
    use \Tuner\Tunable;

    # ⚠️ Do NOT define this:
    // public function send() {}
}
```

> ⚠️ Important: Avoid Defining a send Method
>
> The send() method is already defined in the Tunable trait.
> To avoid conflicts or unexpected behavior, do not override or define your own send() method in any model that uses this trait.

Then, register a route that returns your model using the <ins>**send()**</ins> method:

```php
use Illuminate\Support\Facades\Route;

Route::get('/api/users', function () {
    return \App\Models\User::send();
});
```

<br>

## Step 3: Try It Out

Open your browser or an API client like Postman and try this example request:

<!-- ```

```

✅ This should return a list of users with the Dr prefix in their names, and only the id, name, and email columns in the response.

---
<br> -->

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?columns=id,name,email&search[name]=Dr*</code></pre>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 1, "name": "Dr Foo", "email": ... },
  { "id": 2, "name": "Dr Bar", "email": ... },
  { "id": 3, "name": "Dr Baz", "email": ... }
]
</code></pre>
</div>

</div>

## Next Steps

Now that you’re up and running, explore more features in the [API Reference]({{ "/docs/api-reference.html" | relative_url }}).
