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

---
---

:::warning Manually Loading Assets
The methods below (`getAssets` and `loadAssets`) are only relevant if you are preventing the required JS files from being loaded automatically. See more about [loading assets...](/guides/required-js-assets/)
:::

## getAssets(params = {})

Get a list of required JavaScript assets necessary to render dynamic maps.

Optionally add [parameters](https://developers.google.com/maps/documentation/javascript/url-params) to the Google Maps API URL.

:::code
```twig
{# Get an array of required JavaScript files #}
{% set assets = googleMaps.getAssets() %}
```
```php
// Get an array of required JavaScript files
$assets = GoogleMaps::getAssets();
```
:::

## loadAssets(params = {})

Load all required JavaScript assets necessary to render dynamic maps.

Optionally add [parameters](https://developers.google.com/maps/documentation/javascript/url-params) to the Google Maps API URL. 

:::code
```twig
{# Load the required JavaScript files #}
{% do googleMaps.loadAssets({
    'map_ids': '1234'
}) %}
```
```php
// Load the required JavaScript files
GoogleMaps::loadAssets([
    'map_ids' => '1234'
]);
```
:::

---
---

:::warning More Info
For more information, check out the documentation on [Dynamic Maps](/dynamic-maps/).
:::
