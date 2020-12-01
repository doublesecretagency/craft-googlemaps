# Static Maps

img($locations = [], array $options = []): StaticMap


---
---




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

For more information, check out the documentation on [Dynamic Maps](/dynamic-maps/), [Static Maps](/static-maps/), and the maps [API](/dynamic-maps/api/).
