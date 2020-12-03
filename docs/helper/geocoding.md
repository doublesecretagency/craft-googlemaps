# Geocoding (Address Lookups)

## lookup(target = null)

In order to perform a lookup, pass in a string or [collection of parameters](/geocoding/parameters/) as the `target`.

You can then determine how to retrieve the results of that lookup (`all`, `one`, or `coords`).

:::code
```twig
{# Get all matching results (sorted by best match) #}
{% set allMatches = googleMaps.lookup(target).all() %}

{# Get the first (best) matching result #}
{% set bestMatch = googleMaps.lookup(target).one() %}

{# Get only the coordinates of the first matching address #}
{% set coords = googleMaps.lookup(target).coords() %}
```
```php
// Get all matching results (sorted by best match)
$allMatches = GoogleMaps::lookup($target).all();

// Get the first (best) matching result
$bestMatch = GoogleMaps::lookup($target).one();

// Get only the coordinates of the first matching address
$coords = GoogleMaps::lookup($target).coords();
```
:::

---
---

:::warning More Info
For more information, check out the documentation on [Geocoding](/geocoding/).
:::
