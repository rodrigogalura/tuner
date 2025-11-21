---
title: Projection
level: 2
order: 1
category: basic
---

{% include features.html %}

## Projection (`fields`, `fields!`)

The **projection** feature allows clients to include or exclude specific fields from the API response using query parameters. This helps optimize payload size and gives frontend consumers more control over the data they receive.

<br>

---

### Include Specific Fields

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?fields=id,name,email</code></pre>

<sup>Where <ins>id</ins>, <ins>name</ins>, and <ins>email</ins> are the fields to include in the response.</sup>

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name               | Type   | Description                                 |
|--------------------|--------|---------------------------------------------|
| <ins>fields</ins> | string | Comma-separated list of fields to include. |
</details>

</div>

---

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  {
    "id": 1,
    "name": "Benjamin Heidenreich",
    "email": "quinton42@example.net"
  },
  {
    "id": 2,
    "name": "Jairo Armstrong",
    "email": "damian83@example.org"
  }
]
</code></pre>
</div>

</div>

<br>

---

### Exclude Specific Fields

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?fields!=created_at,updated_at</code></pre>

<sup>Where <ins>created_at</ins> and <ins>updated_at</ins> are the fields to exclude from the response.</sup>

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name                | Type   | Description                                |
|---------------------|--------|--------------------------------------------|
| <ins>fields!</ins> | string | Comma-separated list of fields to exclude. |
</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  {
    "id": 1,
    "name": "Benjamin Heidenreich",
    "email": "quinton42@example.net",
    "email_verified_at": "2025-04-17T01:19:49.000000Z"
  },
  {
    "id": 2,
    "name": "Jairo Armstrong",
    "email": "damian83@example.org",
    "email_verified_at": "2025-04-17T01:19:49.000000Z"
  }
]
</code></pre>
</div>

</div>

<br>
