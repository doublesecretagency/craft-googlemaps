# API

## Universal API

In an effort to smooth the development process, you can effectively call the exact same methods across various languages. Whether you are working in JavaScript, Twig, or PHP, the commands to create a map are all nearly identical.

## Basic Examples

Switch between languages to see the similarities...

:::code
```js
// Very basic
var map = googleMaps.map(locations, options);

// More complex
var map = googleMaps.map()
    .markers(locations, options)
    .styles(stylesArray)
    .kml(url);
```
```twig
{# Very basic #}
{% set map = googleMaps.map(locations, options) %}

{# More complex #}
{% set map = googleMaps.map()
    .markers(locations, options)
    .styles(stylesArray)
    .kml(url) %}
```
```php
// Very basic
$map = GoogleMaps::map($locations, $options);

// More complex
$map = GoogleMaps::map()
    ->markers($locations, $options)
    ->styles($stylesArray)
    ->kml($url);
```
:::

In the examples above, you can see that we are _chaining_ methods together in order to build a map. There are several methods in the API, which can be chained in any order necessary.
 
A chain must always **begin** with the creation of a `map` object.

## Global Methods

### `map(locations, options)`

:::code
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

**Everything starts here.** Use the `map` method to create a new map object.

:::warning Two flavors of "map object"
Internally, there are really two different things that are being referred to as the "map object". When working with Twig or PHP, it will be a [Dynamic Map Model](/models/dynamic-map-model/). But in JavaScript, the map object is actually a self-reference to the `googleMaps` model.

 The structure of this object varies greatly between JS and PHP/Twig, but the API has been designed to make usage nearly identical regardless of language.
:::

:::tip PHP Helper
The PHP version of the API flows through the [`GoogleMaps` Helper Class](/helper/).
:::

#### `locations`

 - Location(s) to appear on the map. See the [Locations](/maps/locations/) page for detailed information.

#### `options`

 - The `options` parameter is where you can configure the map.

> Move map options table here? (Yeah, probably)

#### Returns

 - Map object (for chaining)

:::warning Chainable Map Object
Unless noted otherwise, each of the following methods returns a chainable **map object**. This makes it possible to string together as many of the following methods as needed, in any order you prefer.

Each language has a special command for rendering the map, and thus ending the chain. Make note of how to end the chain for whichever language you are working in.
:::

### `markers(locations, options)`

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

This can be chained to an existing map object. It allows you to place _additional_ markers on the map.

#### `locations`

 - Additional location(s) to appear on the map. See the [Locations](/maps/locations/) page for detailed information.

#### `options`

 - Configure the markers. These options will _only_ apply to the markers created by the corresponding `locations`.

> Move marker options table here? (Yeah, probably)

#### Returns

 - Map object (for chaining)

### `kml(url)`

:::code
```js
map.kml(url);
```
```twig
{% do map.kml(url) %}
```
```php
$map->kml($url);
```
:::

Applies a KML layer to the map.

#### `url`

 - The URL of a given KML layer. It **must** be hosted remotely, and **must** be served over an SSL connection.

#### Returns

 - Map object (for chaining)

### `styles(stylesArray)`

:::code
```js
map.styles(stylesArray);
```
```twig
{% do map.styles(stylesArray) %}
```
```php
$map->styles($stylesArray);
```
:::

#### `stylesArray`

 - A collection of styles, most likely generated elsewhere. See the [Styling a Map](/guides/styling-a-map/) guide for more information.

#### Returns

 - Map object (for chaining)

### `getMap(mapId)`

:::code
```js
var map = googleMaps.getMap(mapId);
```
```twig
{% set map = googleMaps.getMap(mapId) %}
```
```php
$map = GoogleMaps::getMap($mapId);
```
:::

Retrieve an existing map object.

#### `mapId`

 - The ID of the map that you want to access.

#### Returns

 - Map object (for chaining)

### `getMarker(mapId, markerId)`

:::code
```js
var marker = googleMaps.getMarker(mapId, markerId);
```
```twig
{% set marker = googleMaps.getMarker(mapId, markerId) %}
```
```php
$marker = GoogleMaps::getMarker($mapId, $markerId);
```
:::

#### `mapId`

 - The ID of the map that contains the marker.

#### `markerId`

 - The ID of the marker that you want to access.

#### Returns

 - Google Maps [Marker](https://developers.google.com/maps/documentation/javascript/reference/marker) object

---
---

## JavaScript Only

The following methods are only available in JavaScript...

### `hideMarker(markerId)`

```js
map.hideMarker(markerId);
```

Hide a marker.

### `showMarker(markerId)`

```js
map.showMarker(markerId);
```

Show a marker.

### `setMarkerIcon(markerId, iconLiteral)`

```js
map.setMarkerIcon(markerId, iconLiteral);
```

Change the icon of a specified marker.

---
---

## PHP/Twig Only

The following methods are only available in PHP and Twig...

### `getDna()` (magically aliased as `dna` property)

:::code
```twig
{% set dna = map.dna %}
{% set dna = map.getDna() %}
```
```php
$dna = $map->dna;
$dna = $map->getDna();
```
:::

#### Returns

 - The array of DNA which will be used to hydrate the map's container in the DOM.
