---
title: Limitation
level: 2
order: 5
category: basic
---

{% include features.html %}

## 🔢 Limit & Offset (limit, offset)

The **limit/offset** feature provides an alternative to pagination by returning a fixed number of records starting from a specific offset, without pagination metadata.

<br>

---

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?limit=2&offset=3</code></pre>

<sup>Retrieve <ins>2</ins> records starting from the <ins>4th</ins> item (offset is 0-based).</sup>

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name   | Type | Description                                |
|--------|------|--------------------------------------------|
| limit  | int  | Number of records to return                |
| offset | int  | Number of records to skip before returning |
</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>[
  {
    "id": 4,
    "name": "Amelie Crist PhD",
    ...
  },
  {
    "id": 5,
    "name": "Moshe Blick",
    ...
  }
]
</code></pre>

</div>

</div>

<br>

---
