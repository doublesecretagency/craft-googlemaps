# Dynamic Maps

## map(locations = [], options = [])

**Central to the creation of all dynamic maps.** Use this method to create a new map, before further manipulating the map object. See the [complete method details...](/dynamic-maps/map-management/#map-locations-options)

:::code
```twig
{# Generate a new dynamic map #}
{% set map = googleMaps.map(locations) %}
```
```php
// Generate a new dynamic map
$map = GoogleMaps::map($locations);
```
:::

## getMap(mapId)

Call up an existing map using this method. Once you've retrieved the map object, you are free to manipulate it normally. See the [complete method details...](/dynamic-maps/map-management/#getmap-mapid)

:::code
```twig
{# Retrieve an existing dynamic map #}
{% set map = googleMaps.getMap(mapId) %}
```
```php
// Retrieve an existing dynamic map
$map = GoogleMaps::getMap($mapId);
```
:::

## loadAssets()

Only necessary if you've prevented the assets from being loaded automatically. If you haven't explicitly suppressed those files, you will not need to manually load them. See more about [loading assets...](/guides/loading-javascript/)

:::code
```twig
{# Load the required JavaScript files #}
{% do googleMaps.loadAssets() %}
```
```php
// Load the required JavaScript files
GoogleMaps::loadAssets();
```
:::

---
---

:::warning More Info
For more information, check out the documentation on [Dynamic Maps](/dynamic-maps/).
:::
