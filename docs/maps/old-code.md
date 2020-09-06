# OLD CODE

## Creating a dynamic map in PHP

```php
// Set all locations when you create a new map
$map = GoogleMaps::map($locations, $options);

// Get map with basic configuration (no markers)
$map = GoogleMaps::map($locations);

// Append markers using default `markerOptions` values
$map->markers($locations);

// Append markers using specific `markerOptions` override values
$map->markers($locations, $markerOptions);
```


:::code
```js
var map = googleMaps.map(locations, options).markers(locations, markerOptions);
```
```twig
{% set map = googleMaps.map(locations, options).markers(locations, markerOptions) %}
```
```php
$map = GoogleMaps::map($locations, $options)->markers($locations, $markerOptions);
```
:::

:::code
```js
var map = googleMaps.map(locations).kml(links);
```
```twig
{% set map = googleMaps.map(locations).kml(links) %}
```
```php
$map = GoogleMaps::map($locations).kml($links);
```
:::

---

## Creating a dynamic map in Twig

Simply and easily display a normal map.

```twig
{{ googleMaps.map(mapOptions, locations).html() }}
```

Make sure you include the `.html()` call at the end. That ensures that a `<div>` container will be rendered automatically.


---


## Creating a static map in Twig

Simply and easily display a static map.

**Fully rendered `<img>` tag**

```twig
{{ googleMaps.img(mapOptions, locations).html() }}
```

Make sure you include the `.html()` call at the end. That ensures that a `<div>` container will be rendered automatically.

**Just the `src` for the relevant image `<img>` tag**

```twig
{{ googleMaps.img(mapOptions, locations).src() }}
```
