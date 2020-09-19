# Universal Methods

The following methods apply equally, whether you are working in JavaScript, Twig or PHP. These methods have nearly identical parameters and behaviors across all three languages.

However, there are also a few language-specific methods to be aware of. In addition to the Universal Methods below, check out the extended documentation on [JavaScript Methods](/maps/javascript-methods/) and [Twig & PHP Methods](/maps/twig-php-methods/).

## `map(locations, options)`

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

## `markers(locations, options)`

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

## `kml(url, options)`

:::code
```js
map.kml(url, options);
```
```twig
{% do map.kml(url, options) %}
```
```php
$map->kml($url, $options);
```
:::

Applies a KML layer to the map.

#### `url`

 - The URL of a given KML layer. It **must** be hosted remotely, and **must** be served over an SSL connection.
 
#### `options`

 - An object literal in the form of a [KmlLayerOptions](https://developers.google.com/maps/documentation/javascript/reference/kml#KmlLayerOptions) interface.

#### Returns

 - Map object (for chaining)

## `styles(stylesArray)`

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

## `getMap(mapId)`

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

## `getMarker(mapId, markerId)`

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

 - In JavaScript, returns a Google Maps [Marker](https://developers.google.com/maps/documentation/javascript/reference/marker) object.
 - In Twig/PHP, returns an array of marker data.

**Alternate Usage**

It is also possible to call `getMarker` directly from an existing map object...

:::code
```js
var marker = map.getMarker(markerId);
```
```twig
{% set marker = map.getMarker(markerId) %}
```
```php
$marker = $map->getMarker($markerId);
```
:::
