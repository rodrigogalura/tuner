---
title: Frequently Asked Questions
level: 1
order: 3
---

# ❓ Frequently Asked Questions (FAQs)

## 1. Why use Api Igniter instead of Laravel API Resource?

<sup>Laravel’s API Resource is indeed powerful and flexible. However, Api Igniter offers a collection of higher-level features designed to streamline common API tasks like field projection, filtering, relationship expansion, and more — all via intuitive query parameters.</sup>

<br>

---

## 2. Is Api Igniter compatible with Laravel Latest?

<sup>Yes. Api Igniter is fully compatible with:</sup>

<sup>- Laravel versions: 10, 11, and 12</sup>

<sup>- PHP versions: 8.0 up to 8.3</sup> 

> <sup>It follows Laravel’s latest conventions and integrates cleanly into modern Laravel projects.</sup>

<br>

---

## 3. Is Api Igniter prone to SQL Injection?

<sup>It uses Laravel’s Query Builder and Eloquent ORM under the hood, which automatically applies parameter binding to all queries. This ensures that any user-supplied values — such as filters, search terms, or sort parameters — are safely escaped and do not directly affect raw SQL.</sup>

<sup>Api Igniter also sanitizes operators and validates query syntax internally, adding an extra layer of protection.</sup>

> <sup>As always, follow Laravel best practices and avoid exposing raw database input to untrusted users.</sup>

<br>

---

## 4. Does Api Igniter support Eloquent relationships?

<sup>Absolutely. Api Igniter was built with Eloquent in mind. It fully supports all relationship types:</sup>

<sup>- HasOne</sup>

<sup>- HasMany</sup>

<sup>- BelongsTo</sup>

<sup>- BelongsToMany</sup>

> <sup>These relationships can be expanded, filtered, searched, projected, and more — directly from the query string.</sup>

<br>

---

## 5. Can I use Api Igniter in existing Laravel APIs?

<sup>Yes, you can integrate Api Igniter into an existing API with minimal effort.</sup>

> <sup>As long as your models follow standard Eloquent practices, you can immediately start using features like filtering, projection, and expansion in your current endpoints without rewriting your logic.</sup>

<br>

---
<!-- 
## 6. Is Api Igniter configurable?

<sup>Yes, it’s designed with flexibility in mind.</sup>

<sup>You can:</sup>

<sup>- Customize the query parameter names if you prefer different conventions.</sup>

<sup>- Enable or disable specific features.</sup>

<sup>- Extend or override behaviors such as filter operators, field resolution, or pagination format.</sup>

> <sup>A configuration file is published where you can fine-tune how Api Igniter behaves in your application.</sup>

<br>

--- -->

## 6. Does Api Igniter affect performance?

<sup>Api Igniter uses lazy loading prevention and intelligent query optimization under the hood to avoid N+1 problems and unnecessary data loading.</sup>

> <sup>However, like any abstraction, you should benchmark performance on large datasets and use Laravel’s tools like eager loading (with) or indexes as needed.</sup>

<br>

---

## 7. Do you have an API collection to easily explore the features?

<sup>Yes, I provide a [Bruno](https://github.com/rodrigogalura/api-igniter/tree/main/stubs/bruno-collection) collection to help you explore and test the features out of the box.</sup>

> <sup>If you’re using a different API client like *Postman* or *Insomnia*, you can easily convert the Bruno collection using available tools or export options.</sup>

<br>

---
