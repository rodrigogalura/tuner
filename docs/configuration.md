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

Define which columns are allowed for projection.  

```php
protected function getProjectableColumns(): array
{
    return ['*']; // Allow all columns
}
```

<br>

---

## Sorting

Define which columns can be sorted.  

```php
protected function getSortableColumns(): array
{
    return ['*']; // Allow sorting on all columns
}
```

<br>

---

## Search

Define which columns are searchable.  

```php
protected function getSearchableColumns(): array
{
    return ['*']; // Allow searching on all columns
}
```

<br>

---

## Filtering

Define which columns are filterable.  

```php
protected function getFilterableColumns(): array
{
    return ['*']; // Allow filtering on all columns
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

- ['\*'] means all columns are allowed.  
- Replace ['\*'] with an array of specific columns to restrict usage.
**Example:**

```php
protected function getSortableColumns(): array
{
    return ['id', 'name', 'created_at'];
}
```

- limitable() and paginatable() are simple on/off switches — return true to enable, false to disable.

<br>
