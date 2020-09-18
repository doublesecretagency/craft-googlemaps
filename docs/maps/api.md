# API

## Universal API

In an effort to smooth the development process, you can effectively call the exact same methods across various languages. Whether you are working in JavaScript, Twig, or PHP, the commands to create a map are all nearly identical.

## Basic Examples

Switch between languages to see the similarities...

:::code
```js
// Very basic
var map = googleMaps.map().markers(locations, options);

// More complex
var map = googleMaps.map()
    .markers(locations, options)
    .kml(files)
    .styles(stylesArray);
```
```twig
{# Very basic #}
{% set map = googleMaps.map().markers(locations, options) %}

{# More complex #}
{% set map = googleMaps.map()
    .markers(locations, options)
    .kml(files)
    .styles(stylesArray) %}
```
```php
// Very basic
$map = GoogleMaps::map()->markers($locations, $options);

// More complex
$map = GoogleMaps::map()
    ->markers($locations, $options)
    ->kml($files)
    ->styles($stylesArray);
```
:::

There are several methods in the API, which can be chained in any order necessary. However, a chain must always begin with the creation of a `map` object.

## Methods

### `map(locations, options)`

**Everything starts here.** Use the `map` method to create a new map object. The structure of this object varies greatly between JS and PHP/Twig, but the API has been designed to make usage nearly identical regardless of language.

::: code
```js
var map = googleMaps.map(locations, options);
```
```twig
{% set map = googleMaps.map(locations, options) %}
```
```php
$map = GoogleMaps::map($locations, $options);
```
:::

:::tip PHP Helper
The PHP version of the API flows through the [`GoogleMaps` Helper Class](/helper/).
:::

#### `locations`

The `locations` parameter is where to specify which location(s) should appear on the map. See the [Locations](/maps/locations/) page for detailed information.

#### `options`

The `options` parameter is where you can configure the map.

 - Move options table here?

### `markers(locations, options)`

This can be chained to an existing map object. It allows you to place _additional_ markers on the map.

::: code
```js
map.markers(locations, options);
```
```twig
{% do map.markers(locations, options) %}
```
```php
$map->markers($locations, $options);
```
:::

#### `locations`

Specify additional location(s) to appear on the map. See the [Locations](/maps/locations/) page for detailed information.

#### `options`

Configure the markers. These options will _only_ apply to the markers created by the corrosponding `locations`.

 - Move options table here?
