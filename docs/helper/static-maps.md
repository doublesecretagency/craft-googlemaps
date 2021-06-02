---
description:
---

# Static Maps

## img(locations = [], options = [])

**Central to the creation of all static maps.** Use this method to create a new map, before further manipulating the map object. See the [complete method details...](/models/static-map-model/#construct-locations-options)

:::code
```twig
{# Generate a static map #}
{{ googleMaps.img(locations).tag() }}
```
```php
// Generate a static map
GoogleMaps::img($locations)->tag();
```
:::

---
---

:::warning More Info
For more information, check out the documentation on [Static Maps](/static-maps/).
:::
