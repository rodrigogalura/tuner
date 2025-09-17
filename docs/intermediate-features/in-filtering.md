---
title: In Filtering
level: 2
order: 2
category: intermediate
---

{% include features.html %}

## 🌪 In Filtering (`in[column]`)

The **in-filter** allows clients to request resources that match specific values for a column. You can also combine multiple columns using logical operators.

<br>

---

### 🔹 Filter by Multiple IDs

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?in[id]=1,2,3</code></pre>

<sup>Where <ins>id</ins> is the target column for filtering, and <ins>1,2,3</ins> are the specific values to match.</sup>

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name   | Type   | Description                             |
|--------|--------|-----------------------------------------|
| in[id] | string | Comma-separated list of IDs to include. |
</details>

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

### 🔹 Combine with AND Operator

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?in[id]=1,2,3&amp;in[and status]=active,pending</code></pre>

<sup>Where <ins>id, status</ins> are the target columns for filtering, <ins>and</ins> is the logical operator, and <ins>1,2,3, active,pending</ins> are the values to filter by.</sup>

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name           | Type   | Description                                         |
|----------------|--------|-----------------------------------------------------|
| in[id]         | string | Match IDs 1, 2, 3                                   |
| in[and status] | string | AND condition for matching users with status values |
</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 1, "status": "active", ... },
  { "id": 2, "status": "pending", ... }
]
</code></pre>
</div>

</div>

<br>

---

### 🔹 Combine with OR Operator

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?in[id]=1,2&amp;in[or role]=admin,editor</code></pre>

<sup>Where <ins>id, role</ins> are the target columns for filtering, <ins>or</ins> is the logical operator, and <ins>1,2, admin,editor</ins> are the acceptable values.</sup>

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name          | Type   | Description                     |
|---------------|--------|---------------------------------|
| in[id]        | string | Match IDs 1 or 2                |
| in[or status] | string | OR condition for matching roles |
</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 1, "role": "admin", ... },
  { "id": 2, "role": "editor", ... }
]
</code></pre>
</div>

</div>

<br>

---

### 🔹 NOT AND Operator (and!)

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?in[id]=1,2,3&amp;in[and! status]=banned</code></pre>

<sup>Where <ins>id, status</ins> are the target columns for filtering, <ins>and!</ins> is the logical operator with a negation effect, and <ins>1,2,3, banned</ins> are the values to be excluded.</sup>

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name                | Type   | Description                                            |
|---------------------|--------|--------------------------------------------------------|
| in[id]              | string | Filter by specific IDs                                 |
| in[and! created_at] | string | Exclude results where status is "banned" using NOT AND |
</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 1, "status": "active", ... },
  { "id": 3, "status": "pending", ... }
]
</code></pre>
</div>

</div>

<br>

---

### 🔹 NOT OR Operator (or!)

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?in[id]=1,2,3&amp;in[or! role]=banned,guest</code></pre>

<sup>Where <ins>id, role</ins> are the target columns for filtering, <ins>or!</ins> is the logical operator with a negation effect, and <ins>1,2,3, banned,guest</ins> are the values to be excluded.</sup>

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name               | Type   | Description                                                        |
|--------------------|--------|--------------------------------------------------------------------|
| in[id]             | string | Filter by specific IDs                                             |
| in[or! created_at] | string | Exclude if any role matches "banned" or "guest" using NOT OR logic |
</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  { "id": 1, "role": "admin", ... },
  { "id": 2, "role": "editor", ... }
]
</code></pre>
</div>

</div>

<br>

---
