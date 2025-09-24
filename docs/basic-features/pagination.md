---
title: Pagination
level: 2
order: 4
category: basic
---

{% include features.html %}

## Pagination (per-page, page)

The **pagination** feature allows clients to retrieve paginated results along with navigation metadata like total count, current page, and pagination links.

<br>

---

<div style="display: flex; gap: 2rem; align-items: flex-start;" class="req-res">

<div style="flex: 1;" class="highlight">
<strong>Request</strong>

<pre class="highlight"><code>GET /api/users?per-page=3&page=2</code></pre>

<sup>Fetch <ins>3</ins> records per page, displaying page <ins>2</ins>.</sup>

---

<details open class="sup">
<summary><strong>Query Parameters</strong></summary>

| Name                 | Type | Description                                      |
|----------------------|------|--------------------------------------------------|
| <ins>page-size</ins> | int  | Number of items per page                         |
| <ins>page</ins>      | int  | Page number to retrieve (optional, default is 1) |
</details>

</div>

<div style="flex: 1;">
<strong>Response</strong>

<pre><code>{
  "current_page": 2,
  "data": [
    {
      "id": 4,
      "name": "...",
      ...
    },
    {
      "id": 5,
      "name": "...",
      ...
    },
    {
      "id": 6,
      "name": "...",
      ...
    }
  ],
  "first_page_url": "http://localhost/api/users?page=1",
  "from": 4,
  "last_page": 7,
  "last_page_url": "http://localhost/api/users?page=7",
  "links": [
    {
      "url": "http://localhost/api/users?page=1",
      "label": "&laquo; Previous",
      "active": false
    },
    ...
  ],
  "next_page_url": "http://localhost/api/users?page=3",
  "path": "http://localhost/api/users",
  "per_page": 3,
  "prev_page_url": "http://localhost/api/users?page=1",
  "to": 6,
  "total": 20
}
</code></pre>
</div>

</div>

<br>
