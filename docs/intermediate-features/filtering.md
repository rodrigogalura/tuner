---
title: Filtering
level: 2
order: 1
category: intermediate
---

{% include features.html %}

## Filtering (`filter[column]`)

The **filter** query allows you to compare column values using math operators like =, >, >=, <, <=, <>. You can also chain multiple conditions with logical operators.

<br>

---

### Basic Comparison

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?filter[id]=1</code></pre>

<!-- <sup>Where <ins>id</ins> is the target column for filtering, and <ins>3,7</ins> represent the minimum and maximum bounds of the range.</sup> -->

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name       | Type   | Description             |
|------------|--------|-------------------------|
| filter[id] | string | Match users with ID = 1 |
</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 1, "name": "...", ... }
]
</code></pre>
</div>

</div>

<br>

---

### Greater Than

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?filter[id]=>1</code></pre>

<!-- <sup>Where <ins>id</ins> is the target column for filtering, and <ins>3,7</ins> represent the minimum and maximum bounds of the range.</sup> -->

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 2, "name": "...", ... },
  { "id": 3, "name": "...", ... },
  { "id": 4, "name": "...", ... }
]
</code></pre>
</div>

</div>

<br>

---

### Greater Than or Equal To

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?filter[id]=>=1</code></pre>

<!-- <sup>Where <ins>id</ins> is the target column for filtering, and <ins>3,7</ins> represent the minimum and maximum bounds of the range.</sup> -->

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 1, "name": "...", ... },
  { "id": 2, "name": "...", ... },
  { "id": 3, "name": "...", ... },
  ...
]
</code></pre>
</div>

</div>

<br>

---

### Less Than

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?filter[id]=<3</code></pre>

<!-- <sup>Where <ins>id</ins> is the target column for filtering, and <ins>3,7</ins> represent the minimum and maximum bounds of the range.</sup> -->

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 1, "name": "...", ... },
  { "id": 2, "name": "...", ... }
]
</code></pre>
</div>

</div>

<br>

---

### Less Than or Equal To

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?filter[id]=<=3</code></pre>

<!-- <sup>Where <ins>id</ins> is the target column for filtering, and <ins>3,7</ins> represent the minimum and maximum bounds of the range.</sup> -->

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 1, "name": "...", ... },
  { "id": 2, "name": "...", ... },
  { "id": 3, "name": "...", ... }
]
</code></pre>
</div>

</div>

<br>

---

### Not Equal To

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?filter[id]=<>3</code></pre>

<!-- <sup>Where <ins>id</ins> is the target column for filtering, and <ins>3,7</ins> represent the minimum and maximum bounds of the range.</sup> -->

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 1, "name": "...", ... },
  { "id": 2, "name": "...", ... },
  { "id": 4, "name": "...", ... },
  ...
]
</code></pre>
</div>

</div>

<br>

---

## Logical Operators

Like other filters, you can combine filtering rules using:

| Syntax             | Logic Description                        |
|--------------------|------------------------------------------|
| filter[and column]  | All conditions must be true (AND)        |
| filter[or column]   | At least one condition must be true (OR) |
| filter[and! column] | Negate condition (NOT AND)               |
| filter[or! column]  | Negate one of the conditions (NOT OR)    |

### Example: AND Operator

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?filter[id]=>=2&filter[and status]=active</code></pre>

Matches users with <ins>ID >= 2 and status is active</ins>.

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 2, "status": "active", ... },
  { "id": 4, "status": "active", ... },
  ...
]
</code></pre>
</div>

</div>

<br>

---

### Example: OR Operator

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?filter[id]=<3&filter[or status]=guest</code></pre>

Matches users with <ins>ID < 3 or status is guest</ins>.

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 1, ... },
  { "id": 2, ... },
  { "id": 3, "status": "guest", ... },
  ...
]
</code></pre>
</div>

</div>

<br>

---

### Example: NOT Operator

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?filter[id]=<3&filter[and! status]=banned</code></pre>

Excludes users with <ins>status=banned where ID < 3</ins>.

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 1, "status": "active", },
  { "id": 2, "status": "active" },
  ...
]
</code></pre>
</div>

</div>

<br>

---
