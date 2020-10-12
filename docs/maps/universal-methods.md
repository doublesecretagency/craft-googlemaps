# Universal Methods

The following methods apply equally, whether you are working in JavaScript, Twig or PHP. These methods have nearly identical parameters and behaviors across all three languages.

There are also a few language-specific methods to be aware of. In addition to the Universal Methods below, check out the extended documentation for [JavaScript Methods](/maps/javascript-methods/) and [Twig & PHP Methods](/maps/twig-php-methods/).

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

:::tip Get an Existing Map
If you want to access a map object that has _already been created_, use the [`getMap` method](#getmap-mapid).
:::

#### `locations`

 - Location(s) to appear on the map. See the [Locations](/maps/locations/) page for detailed information.

#### `options`

 - The `options` parameter is where you can configure the map.
 
:::warning Available Options
Most, but not all, of these options are available across JavaScript, Twig, and PHP. Please note the few options which are not universally available.
:::
 

| Option               | Available            | Description |
|----------------------|:--------------------:|-------------|
| `id`                 | JavaScript, Twig/PHP | Set the `id` attribute of the map container. |
| `width`              | JavaScript, Twig/PHP | Set the width of the map (in px). |
| `height`             | JavaScript, Twig/PHP | Set the height of the map (in px). |
| `zoom`               | JavaScript, Twig/PHP | Set the default zoom level of the map. <span style="white-space:nowrap">(`1`-`22`)</span> |
| `center`             | JavaScript, Twig/PHP | Set the center position of the map. |
| `styles`             | JavaScript, Twig/PHP | An array of [map styles](/guides/styling-a-map/). |
| `mapOptions`         | JavaScript, Twig/PHP | Accepts any [`google.maps.MapOptions`](https://developers.google.com/maps/documentation/javascript/reference/map#MapOptions) properties. |
| `markerOptions`      | JavaScript, Twig/PHP | Accepts any [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions) properties. |
| `infoWindowOptions`  | JavaScript, Twig/PHP | Accepts any [`google.maps.InfoWindowOptions`](https://developers.google.com/maps/documentation/javascript/reference/info-window#InfoWindowOptions) properties. |
| `infoWindowTemplate` | JavaScript, Twig/PHP | Template path to use for creating [info windows](/maps/info-windows/). |
| `fields`             | Twig/PHP             | Which field(s) of the element(s) should be included on the map. (includes all Address fields by default) |
| `js`                 | Twig/PHP             | Whether to preload the necessary external JavaScript. |

:::tip Additional Options Details
For more info, please consult either the [JavaScript model](/models/javascript/#map-locations-options) or the [Dynamic Map model](/models/dynamic-map-model/#construct-locations-options).
:::

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

:::warning Available Options
Most, but not all, of these options are available across JavaScript, Twig, and PHP. Please note the few options which are not universally available.
:::
 
| Option               | Available            | Description |
|----------------------|:--------------------:|-------------|
| `id`                 | JavaScript, Twig/PHP | Reference point for each marker. |
| `icon`               | JavaScript, Twig/PHP | An `icon` as defined by [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions.icon). |
| `markerOptions`      | JavaScript, Twig/PHP | Accepts any [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions) properties. |
| `infoWindowOptions`  | JavaScript, Twig/PHP | Accepts any [`google.maps.InfoWindowOptions`](https://developers.google.com/maps/documentation/javascript/reference/info-window#InfoWindowOptions) properties. |
| `infoWindowTemplate` | JavaScript, Twig/PHP | Template path to use for creating [info windows](/maps/info-windows/). |
| `fields`             | Twig/PHP             | Which field(s) of the element(s) should be included on the map. (_null_ will include all Address fields) |

:::tip Additional Options Details
For more info, please consult either the [JavaScript model](/models/javascript/#markers-locations-options) or the [Dynamic Map model](/models/dynamic-map-model/#markers-locations-options).
:::

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

 - The URL of a given KML layer. It **must** be hosted remotely. The KML file will not work if hosted locally.
 
#### `options`

 - An object literal in the form of a [KmlLayerOptions](https://developers.google.com/maps/documentation/javascript/reference/kml#KmlLayerOptions) interface.

| Option             | Available            | Description |
|--------------------|:--------------------:|-------------|
| `KmlLayerOptions`  | JavaScript, Twig/PHP | Accepts any [`google.maps.KmlLayerOptions`](https://developers.google.com/maps/documentation/javascript/reference/kml#KmlLayerOptions) properties. |

#### Returns

 - Map object (for chaining)

## `styles(styleSet)`

:::code
```js
map.styles(styleSet);
```
```twig
{% do map.styles(styleSet) %}
```
```php
$map->styles($styleSet);
```
:::

#### `styleSet`

 - A collection of styles, most likely generated elsewhere. See the [Styling a Map](/guides/styling-a-map/) guide for more information.

#### Returns

 - Map object (for chaining)

## `zoom(level)`

```js
map.zoom(10);
```

Change the map's zoom level.

#### `level`

 - The new zoom level. Must be an integer between `1` - `22`.
 
:::tip Zoom Level Reference
 - `1` is zoomed out, a view of the entire planet.
 - `22` is zoomed in, as close to the ground as possible.
:::

## `center(coords)`

```js
map.center({
   "lat": 32.3113966,
   "lng": -64.7527469
});
```

Re-center the map.

#### `coords`

 - A simple key-value set of [coordinates](/models/coordinates/).

## `fit()`

```js
map.fit();
```

Zoom map to automatically fit all markers within the viewing area. Internally uses [`fitBounds`](https://developers.google.com/maps/documentation/javascript/reference/map#Map.fitBounds).

## `refresh()`

```js
map.refresh();
```

Refresh an existing map. You may need to do this after the page has been resized, or if something has been moved or changed.

## `panToMarker(markerId)`

```js
map.panToMarker('33-address');
```

Re-center map on the specified marker.

#### `markerId`

 - The ID of the marker that you want to pan to.

## `setMarkerIcon(markerId, icon)`

```js
map.setMarkerIcon('33-address', 'http://maps.google.com/mapfiles/ms/micons/green.png');
```

Set the icon of an existing marker. Internally uses [`setIcon`](https://developers.google.com/maps/documentation/javascript/reference/marker#Marker.setIcon).

#### `markerId`

 - The ID of the marker that you want to set the icon for.

#### `icon`

 - The icon to set on the specified marker. Can be either a _string_ or an [Icon Interface](https://developers.google.com/maps/documentation/javascript/reference/marker#Icon).

## `hideMarker(markerId)`

```js
map.hideMarker('33-address');
```

Hide a marker. The marker will not be destroyed, it will simply be detached from the map.

#### `markerId`

 - The ID of the marker that you want to hide.

## `showMarker(markerId)`

```js
map.showMarker('33-address');
```

Show a marker. The marker will be re-attached to the map.

#### `markerId`

 - The ID of the marker that you want to show.

---
---

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
