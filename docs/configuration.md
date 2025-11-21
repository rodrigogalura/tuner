---
title: Configuration
level: 1
order: 2
---

# Configuration

By default, Tuner enables most features with minimal setup.  
However, you can **override the default behavior** in your Eloquent models by defining specific configuration methods.

<br>

---

## Projection

Define which fields are allowed for projection.  

```php
protected function getProjectableFields(): array
{
    return ['*']; // Allow all fields
}
```

<br>

---

## Sorting

Define which fields can be sorted.  

```php
protected function getSortableFields(): array
{
    return ['*']; // Allow sorting on all fields
}
```

<br>

---

## Search

Define which fields are searchable.  

```php
protected function getSearchableFields(): array
{
    return ['*']; // Allow searching on all fields
}
```

<br>

---

## Filtering

Define which fields are filterable.  

```php
protected function getFilterableFields(): array
{
    return ['*']; // Allow filtering on all fields
}
```

<br>

---

## Limiting

Control whether the model can be limited with the limit modifier.  

```php
protected function limitable(): bool
{
    return true; // Enable limit for this model
}
```

<br>

---

## Pagination

Control whether the model can use pagination with the per-page modifier.  

```php
protected function paginatable(): bool
{
    return true; // Enable pagination for this model
}
```

<br>

---

## Notes

- ['\*'] means all fields are allowed.  
- Replace ['\*'] with an array of specific fields to restrict usage.
**Example:**

```php
protected function getSortableFields(): array
{
    return ['id', 'name', 'created_at'];
}
```

- limitable() and paginatable() are simple on/off switches — return true to enable, false to disable.

<br>
