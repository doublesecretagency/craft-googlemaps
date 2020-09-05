# Creating a dynamic map in PHP

```php
// Get map with basic configuration (no markers)
$map = GoogleMaps::map($mapOptions);

// Append markers using default `markerOptions` values
$map->markers($locations);

// Append markers using specific `markerOptions` override values
$map->markers($locations, $markerOptions);
```

```php
// Set all locations when you create a new map
$map = GoogleMaps::map($mapOptions, $locations);
```


:::code
```js
var map = googleMaps.map(options).markers(locations, markerOptions);
```
```twig
{% set map = googleMaps.map(options).markers(locations, markerOptions) %}
```
```php
$map = GoogleMaps::map($options)->markers($locations, $markerOptions);
```
:::

:::code
```js
var map = googleMaps.map().markers(locations).kml(link);
```
```twig
{% set map = googleMaps.map().markers(locations).kml(link) %}
```
```php
$map = GoogleMaps::map()->markers($locations).kml($link);
```
:::
