# Chaining Methods

Whether you are using JavaScript, Twig, or PHP, the same format can be used to create a map...

:::code
```js
var map = googleMaps.map(mapOptions).markers(locations);
```
```twig
{% set map = googleMaps.map(mapOptions).markers(locations) %}
```
```php
$map = GoogleMaps::map($mapOptions).markers($locations);
```
:::

This is an extremely simple example. Chaining methods allows you to build a map in whichever way is required. There are several compatible methods which can be chained in any order.

## `map`

This is the method which starts the chain. On its own, it creates a [Map Model]().

### `map(mapOptions = {}, locations = null, markerOptions = {})`



## `markers`

This method allows you to append markers onto an existing map object. You can pass a single location, or an array of locations.














In order to provide consistency between languages, the following functions are equally available in PHP, Twig, and JavaScript.

```js
// Create a new map object
map(mapOptions = {}, locations = null, markerOptions = {})

// Add markers to a map object
markers(locations, markerOptions = {})
```

Across PHP, Twig, and JavaScript, there is a common syntax for building your maps & markers.

1. First, create a **map**.
2. Then, append **markers**.

It's that simple! Take a look at the examples below, you will see how this pattern translates across programming languages.

## Simple examples

::: code
```js
var map = googleMaps.map(mapOptions).markers(locations, markerOptions);
// or
var map = googleMaps.map(mapOptions, locations, markerOptions);
```
```twig
{% set map = googleMaps.map(mapOptions).markers(locations, markerOptions) %}
{# or #}
{% set map = googleMaps.map(mapOptions, locations, markerOptions) %}
```
```php
$map = GoogleMaps::map($mapOptions)->markers($locations, $markerOptions);
// or
$map = GoogleMaps::map($mapOptions, $locations, $markerOptions);
```
:::

When you call the `map` method, you can pass in a `mapOptions` array of properties to configure the map. Once you have a map object, you will be able to call `markers` to append new markers to the map.

## Passing markers directly

If you don't need anything fancy out of your markers, you can actually pass them in as the second parameter of your `map` function.

::: code
```js
googleMaps.map(mapOptions, locations)
```
```twig
googleMaps.map(mapOptions, locations)
```
```php
GoogleMaps::map($mapOptions, $locations)
```
:::

## Formatting markers

You can format your markers by passing in `markerOptions`.

::: code
```js
map.markers(locations, markerOptions)
```
```twig
map.markers(locations, markerOptions)
```
```php
$map->markers($locations, $markerOptions);
```
:::

## Formatting groups of markers

If you have multiple groups of markers, you can format an entire batch by specifying new marker options each time.

::: code
```js
// Create a new map object
var map = googleMaps.map(mapOptions);

// Add first group of locations with custom marker options
map.markers(locationsOne, markerOptionsOne);

// Add second group of locations with custom marker options
map.markers(locationsTwo, markerOptionsTwo);
```
:::
