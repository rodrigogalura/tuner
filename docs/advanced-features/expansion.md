---
title: Expansion
level: 2
category: advanced
---

{% include features.html %}

## Expansion

The **Expansion** feature in **Tuner** allows API consumers to include related models directly in the response.  
This removes the need for multiple requests and gives clients more control over the data they need.

<br>

---

## Supported Relationships

Tuner supports all common Eloquent relationship types:

- **HasOne**
- **HasMany**
- **BelongsTo**
- **BelongsToMany**

<br>

---

## Setup

By default, expansion is **disabled**.  
To enable it, you need to define the `getExpandableRelations` method in your model and provide relation settings.

### Example

```php
class User extends Model
{
    public function phone() // <-- relation name
    {
        return $this->hasOne(Phone::class);
    }

    protected function getExpandableRelations(): array
    {
        return [
            'phone' => [ // <-- relation name
                'options' => [
                    'projectable_fields' => ['*'],
                    'sortable_fields'   => ['*'],
                    'searchable_fields' => ['*'],
                    'filterable_fields' => ['*'],
                ],

                'table' => 'phones',  // optional
                'fk'    => 'user_id', // optional
            ],
        ];
    }
}
```

**Explanation:**

- phone — The relation name (must match your Eloquent relationship method).
- options — Defines which modifiers can be applied on this relation.
- table — (Optional) Explicitly specify the related table.
- fk — (Optional) Explicitly specify the foreign key.

<br>

---

## Usage

You can use the `expand` modifier to load related resources.  
When expanding, you can assign an **alias** to the relation.  
This alias is required if you want to apply other modifiers such as `fields`, `sort`, or `filter`.

### Example

<pre><code>GET /api/users?expand[posts]=p&p_fields=id,title</code></pre>

**Explanation:**

- expand — The modifier used to expand relationships.
- posts — The relation of the subject (users) into the object resource (posts).
- p — The alias assigned to the relation.
- p_fields — Alias(<ins>p</ins>) + Separator(<ins>\_</ins>) + Modifier(<ins>fields</ins>) (in this case, projection with fields).

The response will only include the id and title fields from the expanded posts relation.

<br>

---

### Combining with Modifiers

Expansion works seamlessly with other Tuner modifiers:

<!-- **Projection (fields)**

<pre><code>GET /api/users?expand[posts]=p&p_fields=id,title</code></pre> -->

**Sort (sort)**

<pre><code>GET /api/users?expand[posts]=p&p_sort[created_at]=desc</code></pre>

**Search (search)**

<pre><code>GET /api/users?expand[posts]=p&p_search[title]=*hello*</code></pre>

**Filter (filter, in, between)**

<pre><code>GET /api/users?expand[posts]=p&p_filter[status]=published
GET /api/users?expand[posts]=p&p_in[id]=1,2,3
GET /api/users?expand[posts]=p&p_between[created_at]=2000-01-01,2010-01-01
</code></pre>

### Supported Operators

Expansion supports the same operators as other modifiers:

- **Relational operators**: =, <>, <, >, <=, >=
- **Logical operators**: AND, OR, AND!, OR!

<br>

---

### Best Practices

- Use expansion only for the data you really need.
  Over-expanding can impact performance.
- Always provide an alias when you plan to apply other modifiers (fields, sort, filter).
- Combine fields with expand to keep responses lightweight.
- Sort and filter expanded relationships carefully to avoid unexpected results.

> Expansion makes your APIs smarter and more consumer-friendly — clients can tune the exact shape of the response they want.

<br>
