---
title: Expanding
level: 2
category: advanced
---

{% include features.html %}

## 🔗 Expanding (expand[relation]=alias)

The **expanding** feature allows clients to include related models in the API response by specifying the relationships to expand. This enables nested resource inclusion with optional features like projecting, filtering, sorting, searching, and more—depending on the relationship type.

> ⚠️ Prerequisite: Defining Relationships
>
> <sup>Before using the expand feature, you must define the appropriate relationships in your Eloquent models. This feature relies on Laravel’s relationship methods (e.g., hasOne, hasMany, belongsTo, belongsToMany) to retrieve related data dynamically.</sup>
>
> <sup>Since this feature is more advanced than basic filtering or projection, a properly configured model relationship is required. Otherwise, the expansion will not work.</sup>
>
> <sup>For example, to expand a HasOne relationship like:</sup>
>
> <pre class="highlight"><code>GET /api/users?expand[phone]=p</code></pre>
>
> <sup>Your <ins>User</ins> model should have a method named <ins>phone()</ins> that defines this relationship:</sup>
>
> ```php
> use Illuminate\Foundation\Auth\User as Authenticatable;
>
> class User extends Authenticatable
> {
>     // ...
>
>     public function phone()
>     {
>         return $this->hasOne(Phone::class);
>     }
>
>     // ...
> }
> ```
>
> <sup>Make sure the method name (e.g., phone) matches the key used in the expand query (i.e., expand[phone]).</sup>

<br>

---

### 🔗 Has One

Expands a one-to-one related resource.

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?expand[phone]=p</code></pre>

<sup>Where <ins>phone</ins> is the relationship to expand and <ins>p</ins> is the alias used for field projection.</sup>

<sup>You can learn more about alias usage in projections [here](#using-aliases-with-field-projection).</sup>

---

<details open class="sup">
<summary><strong>Supported Capabilities</strong></summary>

✅ Projecting Fields
</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  {
    "id": 1,
    "name": "Dortha Cremin",
    ...
    "phone": {
      "id": 1,
      "number": "531-297-3475",
      "user_id": 1,
      ...
    }
  },
  ...
]
</code></pre>
</div>

</div>

<br>

---

### 🔗 Belongs To

Expands a many-to-one related resource.

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/phones?expand[user]=u</code></pre>

<sup>Where <ins>user</ins> is the relationship to expand and <ins>u</ins> is the alias used in field projection.</sup>

---

<details open class="sup">
<summary><strong>Supported Capabilities</strong></summary>

✅ Projecting Fields
</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  {
    "id": 1,
    "number": "531-297-3475",
    ...
    "user": {
      "id": 1,
      "name": "Dortha Cremin",
      ...
    }
  },
  ...
]
</code></pre>
</div>

</div>

<br>

---

### 🔗 Has Many

Expands a one-to-many related resource. Supports projecting, searching, sorting, filtering, in filtering, and between filtering.

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?expand[posts]=p</code></pre>

<sup>Where <ins>posts</ins> is the relationship to expand and <ins>p</ins> is the alias used in sub-query customization.</sup>

---

<details open class="sup">
<summary><strong>Supported Capabilities</strong></summary>

✅ Projecting Fields

✅ Searching

✅ Sorting

✅ Filtering

✅ In Filtering

✅ Between Filtering

</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  {
    "id": 1,
    "name": "Dortha Cremin",
    ...
    "posts": [
      {
        "id": 1,
        "title": "laudantium",
        ...
      },
      ...
    ]
  },
  ...
]
</code></pre>
</div>

</div>

<br>

---

### 🔗 Belongs To Many

Expands a many-to-many related resource. Supports projecting, searching, sorting, filtering, in filtering, and between filtering.

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?expand[roles]=r</code></pre>

<sup>Where <ins>roles</ins> is the relationship to expand and <ins>r</ins> is the alias used in sub-query customization.</sup>

---

<details open class="sup">
<summary><strong>Supported Capabilities</strong></summary>

✅ Projecting Fields

✅ Searching

✅ Sorting

✅ Filtering

✅ In Filtering

✅ Between Filtering

</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  {
    "id": 1,
    "name": "Dortha Cremin",
    ...
    "roles": [
      {
        "id": 1,
        "name": "tus",
        ...
        "pivot": {
          "user_id": 1,
          "role_id": 1
        }
      },
      ...
    ]
  },
  ...
]
</code></pre>
</div>

</div>

<br>

---

### 🔗 Using Aliases with Field Projection

When expanding relationships, you can define an alias to reference the expanded entity more concisely in your query parameters—especially useful for projecting specific fields within that relationship.

For example, to expand the phone relationship of users and project only selected fields, use:

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?expand[phone]=p&p_fields=id,number</code></pre>

<sup>Where <ins>phone</ins> is the relationship, <ins>p</ins> is the alias, and <ins>p_fields</ins> specifies which fields of phone to include.</sup>

---

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name          | Type   | Description                                                      |
|---------------|--------|------------------------------------------------------------------|
| expand[phone] | string | Expands the phone relationship. The value p defines its alias.   |
| p_fields      | string | Comma-separated list of fields to include in the expanded phone. |
</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  {
    "id": 1,
    "name": "...",
    "phone": {
      "id": 1,
      "number": "531-297-3475",
      "user_id": 1
    }
  },
  {
    "id": 2,
    "name": "...",
    "phone": {
      "id": 2,
      "number": "(320) 663-1502",
      "user_id": 2
    }
  }
]
</code></pre>
</div>

</div>

> ℹ️ Note:
>
> <sup>Foreign keys like <code>user_id</code> are automatically included in the response for relational context, even if not explicitly listed in <code>p_fields</code>.</sup>

<br>

---
