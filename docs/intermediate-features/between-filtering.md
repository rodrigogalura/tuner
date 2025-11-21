---
title: Between
level: 2
order: 3
category: intermediate
---

{% include features.html %}

## Filter (`between[field]=min,max`)

The **between feature** allows you to filter results based on a range of values for one or more fields. It also supports combining multiple conditions using logical operators like AND, OR, and their negated versions.

<br>

---

### Filter by ID Range

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?between[id]=3,7</code></pre>

<sup>Where <ins>id</ins> is the target field for filtering, and <ins>3,7</ins> represent the minimum and maximum bounds of the range.</sup>

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name                   | Type   | Description                                            |
|------------------------|--------|--------------------------------------------------------|
| <ins>between</ins>[id] | string | Comma-separated values defining the min and max range. |
</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 3, "name": "Mr. Price Mosciski Sr.", ... },
  { "id": 4, "name": "Amelie Crist PhD", ... },
  { "id": 5, "name": "Moshe Blick", ... },
  { "id": 6, "name": "Mr. Alfred O'Reilly", ... },
  { "id": 7, "name": "Dr. Clement Bogan", ... }
]
</code></pre>
</div>

</div>

<br>

---

### Combine with AND Operator

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?between[id]=1,3&amp;between[and created_at]=2025-04-01,2025-04-17</code></pre>

<sup>Where <ins>id, created</ins> are the target fields for filtering, <ins>and</ins> is the logical operator, and <ins>1,3, 2025-04-01,2025-04-17</ins> represent the min/max bounds of their respective ranges.</sup>

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name                               | Type   | Description                                       |
|------------------------------------|--------|---------------------------------------------------|
| <ins>between</ins>[id]             | string | Comma-separated ID bounds.                        |
| <ins>between</ins>[and created_at] | string | Apply AND logic between ID and created_at ranges. |
</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 1, ..., "created_at": "2025-04-05", ... },
  { "id": 3, ..., "created_at": "2025-04-12", ... }
]
</code></pre>
</div>

</div>

<br>

---

### Combine with OR Operator

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?between[id]=3,5&amp;between[or created_at]=2025-04-01,2025-04-17</code></pre>

<sup>Where <ins>id, created</ins> are the target fields for filtering, <ins>OR</ins> is the logical operator, and <ins>3,5, 2025-04-01,2025-04-17</ins> represent the min/max bounds of the range filters.</sup>

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name                              | Type   | Description                                     |
|-----------------------------------|--------|-------------------------------------------------|
| <ins>between</ins>[id]            | string | Comma-separated ID bounds.                      |
| <ins>between</ins>[or created_at] | string | Apply OR logic between ID or created_at ranges. |
</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 3, ..., "created_at": "2025-04-12", ... }
  { "id": 4, ..., "created_at": "2025-04-15", ... },
  { "id": 5, ..., "created_at": "2025-04-15", ... },
  { "id": 6, ..., "created_at": "2025-04-17", ... },
  { "id": 7, ..., "created_at": "2025-04-17", ... }
]
</code></pre>
</div>

</div>

<br>

---

### NOT AND Operator (and!)

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?between[id]=3,5&amp;between[and! created_at]=2025-04-01,2025-04-17</code></pre>

<sup>Where <ins>id, created</ins> are the target fields for filtering, <ins>and!</ins> is the logical operator with a negation effect, and <ins>3,5, 2025-04-01,2025-04-17</ins> represent the min/max bounds to be excluded.</sup>

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name                                | Type   | Description                                                             |
|-------------------------------------|--------|-------------------------------------------------------------------------|
| <ins>between</ins>[id]              | string | Comma-separated ID bounds.                                              |
| <ins>between</ins>[and! created_at] | string | Apply NOT AND - match ID range but exclude those within the date range. |
</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 3, ..., "created_at": "2025-04-12", ... }
  { "id": 5, ..., "created_at": "2025-04-15", ... },
]
</code></pre>
</div>

</div>

<br>

---

### NOT OR Operator (or!)

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?between[id]=3,5&amp;between[or! created_at]=2025-04-01,2025-04-17</code></pre>

<sup>Where <ins>id, created</ins> are the target fields for filtering, <ins>or!</ins> is the logical operator with a negation effect, and <ins>3,5, 2025-04-01,2025-04-17</ins> represent the min/max bounds to be excluded.</sup>

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name                               | Type   | Description                                                         |
|------------------------------------|--------|---------------------------------------------------------------------|
| <ins>between</ins>[id]             | string | Comma-separated ID bounds.                                          |
| <ins>between</ins>[or! created_at] | string | Apply NOT OR - exclude results matching any part of the date range. |
</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 3, ..., "created_at": "2025-03-12", ... }
  { "id": 5, ..., "created_at": "2025-04-18", ... },
]
</code></pre>
</div>

</div>

<br>
