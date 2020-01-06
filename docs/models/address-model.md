# Address Model

The properties and methods of the Address Model are identical whether you are accessing them via Twig or PHP.

```twig
{% set address = entry.myAddressField %}
```

```php
$address = $entry->myAddressField;
```

::: warning ADDITIONAL PROPERTIES AND METHODS
The Address Model is an extension of the [Location Model](/models/location-model/). It contains all properties and methods of the Location Model, plus the properties and methods shown below.

You can access `lat` and `lng` just as easily as `street1` and `street2`.
:::

## Public Properties

### `street1`

_string_ - The first line of the street address. Usually contains the street name & number of the location.

### `street2`

_string_ - The second line of the street address. Usually contains the apartment, unit, or suite number.

### `city`

_string_ - The city of the location.

### `state`

_string_ - The state or province of the location.

### `zip`

_string_ - The zip or postal code of the location.

### `country`

_string_ - The country of the location.

### `distance`

_float_ - Alias for `getDistance()`.

## Public Methods

### `getDistance(coords = false, units = 'miles')`

The distance between this address and your proximity search target. Will be returned in whatever unit of measurement (miles or kilometers) was specified in the proximity search parameters.

::: warning PROXIMITY SEARCH ONLY
This value will only be available if the address was returned as part of a [proximity search](/proximity-search/). Otherwise, the value for `distance` will be null.
:::

Behaves just as described in the [Location Model](/models/location-model/#getdistance-coords-units-mi), you can optionally pass in coordinates to measure the distance between two points. However, it is slightly more powerful in the context of an Address Model.

If the address has been returned as part of a proximity search, the method will use the coordinates of your search target by default.

### `isEmpty()`

_bool_ - Returns whether _all_ of the address fields (excluding `country`) are empty, or whether they contain any data at all. Specifically looks for data in any of the following subfields:

 - `street1`
 - `street2`
 - `city`
 - `state`
 - `zip`

```twig
{% if not address.isEmpty %}
    {{ address.format() }}
{% endif %}
```

### `format(mergeUnit = false, mergeCity = false)`

- `mergeUnit` - Whether to merge the apartment or suite number into the preceding line.
- `mergeCity` - Whether to merge the city and state into the preceding line.

**Examples:**

```twig
{# Line breaks before both the unit number and the city #}
{{ address.format() }}
```

>123 Main St<br>
>Suite #101<br>
>Springfield, CO 81073

```twig
{# Line break just before the city #}
{{ address.format(true) }}
```

>123 Main St, Suite #101<br>
>Springfield, CO 81073

```twig
{# No line breaks #}
{{ address.format(true, true) }}
```

>123 Main St, Suite #101, Springfield, CO 81073

::: tip NOTE
You will rarely need to use `.format(true, true)`. If you really want to render the address on a single line, just **output the model directly** instead (see below).
:::

## Outputting the model directly

When you output the model directly, it will render the entire address on a single line.

```twig
{{ address }}
{# 123 Main St, Suite #101, Springfield, CO 81073 #}
```

In other words, these two statements are identical...

```twig
{{ entry.myAddressField }}
{# ... is just an alias of... #}
{{ entry.myAddressField.format(true, true) }}
```

You will rarely need to use `.format(true, true)`. If possible, it's always preferred to simply output the model directly.
