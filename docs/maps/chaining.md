# Chaining objects to create a map

In order to provide consistency between languages, the following functions are equally available in PHP, Twig, and JavaScript.

```js
// Create a new map object
map(mapOptions = {}, locations = null)

// Add markers to a map object
markers(locations, markerOptions = {})
```

Across PHP, Twig, and JavaScript, there is a common syntax for building your maps & markers.

1. First, create a **map**.
2. Then, append **markers**.

It's that simple! Take a look at the examples below, you will see how this pattern translates across programming languages.

## Simple examples

::: code
```twig
{% set map = googleMaps.map(mapOptions).markers(locations) %}
```
```js
var map = googleMaps.map(mapOptions).markers(locations);
```
```php
$map = GoogleMaps::map($mapOptions)->markers($locations);
```
:::

When you call the `map` method, you can pass in a `mapOptions` array of properties to configure the map. Once you have a map object, you will be able to call `markers` to append new markers to the map.

## Passing markers directly

If you don't need anything fancy out of your markers, you can actually pass them in as the second parameter of your `map` function.

**PHP**
```php
GoogleMaps::map($mapOptions, $locations)
```

**Twig**
```twig
googleMaps.map(mapOptions, locations)
```

**JavaScript**
```js
googleMaps.map(mapOptions, locations)
```

## Formatting markers

You can format your markers by passing in `markerOptions`.

**PHP**
```php
$map->markers($locations, $markerOptions);
```

**Twig**
```twig
map.markers(locations, markerOptions)
```

**JavaScript**
```js
map.markers(locations, markerOptions)
```

## Formatting groups of markers

If you have multiple groups of markers, you can format an entire batch by specifying new marker options each time.

** JavaScript **
```js
// Create a new map object
var map = googleMaps.map(mapOptions);

// Add first group of locations with custom marker options
map.markers(locationsOne, markerOptionsOne);

// Add second group of locations with custom marker options
map.markers(locationsTwo, markerOptionsTwo);
```

