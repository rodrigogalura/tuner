---
title: Searching
level: 2
order: 3
category: basic
---

{% include features.html %}

## Searching (`search[column]`)

The **searching** feature allows clients to perform keyword-based searches across defined columns. It’s useful for implementing search bars or global search functionalities in applications.

**You can use:**
- `term` → match anywhere
- `*term*` → match anywhere
- `term*` → match at the beginning
- `*term` → match at the end

<br>

---

### Both-Side Wildcard (`*term*`)

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?search[name]=*III*</code></pre>

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name          | Type   | Description                                |
|---------------|--------|--------------------------------------------|
| search[column] | string | Wildcard pattern to search within a column. |
</details>
</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  {
    "id": 5,
    "name": "Dr. Cooper Blanda III",
    ...
  },
  {
    "id": 13,
    "name": "Dr. Tad Beer III",
    ...
  },
  {
    "id": 48,
    "name": "Mr. Kim Johnson III",
    ...
  }
]
</code></pre>
</div>

</div>

<br>

---

### Postfix Wildcard (`term*`)

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?search[name]=Dr*</code></pre>

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name          | Type   | Description                                |
|---------------|--------|--------------------------------------------|
| search[column] | string | Match values starting with given term.     |
</details>
</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  {
    "id": 5,
    "name": "Dr. Cooper Blanda III",
    ...
  },
  {
    "id": 13,
    "name": "Dr. Tad Beer III",
    ...
  }
]
</code></pre>
</div>

</div>

<br>

---

### Prefix Wildcard (`*term`)

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?search[email]=*org</code></pre>

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name          | Type   | Description                                |
|---------------|--------|--------------------------------------------|
| search[column] | string | Match values ending with given term.       |
</details>
</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  {
    "id": 2,
    "name": "Jairo Armstrong",
    "email": "damian83@example.org",
    ...
  },
  {
    "id": 4,
    "name": "Jacinthe Stamm",
    "email": "tmayert@example.org",
    ...
  }
]
</code></pre>
</div>

</div>

<br>

---

> You can chain multiple columns using `search[column1,column2,columnN]=term` for compound searches.
