# Dynamic Maps

loadAssets()
map($locations = [], array $options = []): DynamicMap
getMap(string $mapId)


---
---



:::code
```twig
{# Generate a dynamic map #}
{{ googleMaps.map(locations).tag() }}

{# Retrieve an existing dynamic map #}
{% set map = googleMaps.getMap(mapId) %}
```
```php
// Generate a dynamic map
GoogleMaps::map($locations)->tag();

// Retrieve an existing dynamic map
$map = GoogleMaps::getMap($mapId);
```
:::
