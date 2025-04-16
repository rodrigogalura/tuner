# 🤝 Contributing to Api Igniter

First off, thanks for taking the time to contribute to **Api Igniter**! 🎉  
Whether it’s fixing a bug, improving docs, or suggesting a new feature—you’re helping make API development easier for everyone.

---

> [!IMPORTANT]
> ## Development Requirements
>
> - **PHP 8.3.\***
> - **Composer 2.***

## 🛠️ Local Development Setup
```
git clone git@github.com:rodrigogalura/api-igniter.git
composer install
```

---

## Run tests

We use [Pest](https://pestphp.com) for testing

```
php vendor/bin/pest
```

---

## Writing Tests

All features must include corresponding tests.
•   Please follow Pest-style syntax for consistency.
•   Place your tests in the /tests directory.


## Roadmap

**1\.**
**2\.**
**3\.**
**4\.**
**5\. Support filtering, sorting, and pagination on collections.**

### Filtering

**Default Filtering:**
- [x] - Math Operators
    - [x] - Equal =
    - [x] - Not Equal <>
    - [x] - Greater Than >
    - [x] - Greater Than or Equal >=
    - [x] - Less Than <
    - [x] - Less Than or Equal <=
- [x] - Logical Operators
    - [x] - AND
    - [x] - OR
    - [x] - AND!
    - [x] - OR!

**Search Filtering:**
- [x] - Pre-Wildcard
- [x] - Post-Wildcard
- [x] - Pre-Post-Wildcard

**In Filtering:**
- [x] - In operator

**Between Filtering:**
- [x] - Between operator

**Sorting**
- [x] - Ascending
- [x] - Descending
    - [x] - d
    - [x] - des
    - [x] - desc
    - [x] - descending
    - [x] - -

**Pagination**

- [x] - Paginate


6\. Support link expansion of relationships. Allow clients to expand the data contained in the response by including additional representations instead of, or in addition to, links.

### Expansion
- [x] - Expand
- [x] - Expand with:
- [x] Projectable
- [x] Filtering
    - [x] Default Filter
    - [x] Search Filter
    - [x] In Filter
    - [x] Between Filter
 - [x] Sortable

7\. Support field projections on resources. Allow clients to reduce the number of fields that come back in the response.

- [x] - Field projections
    - [x] - Include
    - [x] - Exclude


### Cheat Sheet Reference

https://github.com/RestCheatSheet/api-cheat-sheet

## PR Guidelines

## License

MIT © rodrigogalura
