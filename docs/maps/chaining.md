# (OLD) Chaining




Chaining methods allows you to build a map in whichever way is required. There are several compatible methods which can be chained in any order.

## `map`

This is the method which starts the chain. On its own, it creates a [Dynamic Map Model](/models/dynamic-map-model/).

### `map(locations, options = {})`



## `markers`

### `markers(locations, options = {})`

Append additional markers to an existing map. You can specify the `locations` is a set of locations, `options` is a set of options.

This method allows you to append markers onto an existing map object. You can pass a single location, or an array of locations.



## `kml`
### `kml(files, options = {})`

## `styles`
### `styles(styles)`








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

## Simple Examples

::: code
```js
var map = googleMaps.map().markers(locations, options);
```
```twig
{% set map = googleMaps.map().markers(locations, options) %}
```
```php
$map = GoogleMaps::map()->markers($locations, $options);
```
:::

### Configuring a Map

::: code
```js
googleMaps.map(locations, options)
```
```twig
googleMaps.map(locations, options)
```
```php
GoogleMaps::map($locations, $options)
```
:::

### Formatting a group of markers

::: code
```js
googleMaps.map().markers(locations, options)
```
```twig
googleMaps.map().markers(locations, options)
```
```php
GoogleMaps::map()->markers($locations, $options);
```
:::

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
