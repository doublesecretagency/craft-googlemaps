---
description: Control your maps equally with JavaScript, Twig, or PHP. Available in multiple languages, these universal methods can be chained to create complex maps.
---

# Universal Methods

The following methods apply equally, whether you are working in JavaScript, Twig, or PHP. These methods have nearly identical parameters and behaviors across all three languages.

:::warning The Magic of Chaining
Each of these methods can be [chained together](/dynamic-maps/chaining/) in any order you'd like. Chaining can be a powerful technique, allowing you to build complex maps with ease.
:::

There are also a few language-specific methods to be aware of. In addition to the Universal Methods below, check out the extended documentation for [JavaScript Methods](/dynamic-maps/javascript-methods/) and [Twig & PHP Methods](/dynamic-maps/twig-php-methods/).

## `markers(locations, options)`

:::code
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

Places additional markers onto the map.

#### Arguments

- `locations` (_[locations](/dynamic-maps/locations/)_) - Will be used to create additional markers for the map.
- `options` (_array_|_null_) - The `options` parameter allows you to configure the markers in greater detail. These options will _only_ apply to the markers created in this method call.

:::warning Available Options
Most, but not all, of these options are available across JavaScript, Twig, and PHP. Please note the few options which are not universally available.
:::
 
| Option               | Available            | Description
|----------------------|:--------------------:|-------------
| `id`                 | JavaScript, Twig/PHP | Reference point for each marker.
| `icon`               | JavaScript, Twig/PHP | An `icon` as defined by [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions.icon).
| `markerOptions`      | JavaScript, Twig/PHP | Accepts any [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions) properties.
| `infoWindowOptions`  | JavaScript, Twig/PHP | Accepts any [`google.maps.InfoWindowOptions`](https://developers.google.com/maps/documentation/javascript/reference/info-window#InfoWindowOptions) properties.
| `infoWindowTemplate` | Twig/PHP             | Template path to use for creating [info windows](/dynamic-maps/info-windows/).
| `infoWindowData`     | Twig/PHP             | Additional data for passing into an [info window](/dynamic-maps/info-windows/).
| `markerLink`         | JS/Twig/PHP          | URL to go to when marker is clicked.
| `markerClick`        | JS/Twig/PHP          | JS callback function triggered when marker is clicked.
| `field`              | Twig/PHP             | Address field(s) to be included on the map. (includes all by default)

:::tip Additional Options Details
For more info, please consult either the [JavaScript object](/javascript/dynamicmap.js/#markers-locations-options) or the [Dynamic Map model](/models/dynamic-map-model/#markers-locations-options).
:::

## `circles(locations, options)`

:::code
```js
map.circles(locations, options);
```
```twig
{% do map.circles(locations, options) %}
```
```php
$map->circles($locations, $options);
```
:::

Places one or more circles onto the map.

#### Arguments

- `locations` (_[locations](/dynamic-maps/locations/)_) - Will be used to add circles to the map.
- `options` (_array_|_null_) - The `options` parameter allows you to configure circles in greater detail.

| Option          | Available            | Description |
|-----------------|:--------------------:|-------------|
| `id`            | JavaScript, Twig/PHP | Reference point for each circle. |
| `circleOptions` | JavaScript, Twig/PHP | Accepts any [`google.maps.CircleOptions`](https://developers.google.com/maps/documentation/javascript/reference/polygon#CircleOptions) properties. |

:::tip How to Draw Circles
For a more elaborate explanation, see the guide for [Drawing Circles...](/guides/drawing-circles/)
:::

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

#### Arguments

- `url` (_string_) - The URL of a given KML layer. It **must** be publicly hosted on the internet. Google needs to parse the KML file, so it will not work if hosted locally or behind a private URL.
- `options` (_array_|_null_) - The `options` parameter allows you to configure this KML layer in greater detail.

| Option             | Available            | Description |
|--------------------|:--------------------:|-------------|
| `id`               | JavaScript, Twig/PHP | Reference point for each KML layer. |
| `kmlLayerOptions`  | JavaScript, Twig/PHP | Accepts any [`google.maps.KmlLayerOptions`](https://developers.google.com/maps/documentation/javascript/reference/kml#KmlLayerOptions) properties. |

:::warning ID required to dynamically adjust KML layers
In order to [further manipulate](/guides/kml-layers/#further-manipulating-kml-layers) a KML layer, you must provide an `id` option value.
:::

For more information, see the [KML Layers](/guides/kml-layers/) guide.

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

#### Arguments

- `styleSet` (_array_) - A collection of styles, most likely generated elsewhere.
 
For more information, see the [Styling a Map](/guides/styling-a-map/) guide.

## `zoom(level)`

:::code
```js
map.zoom(level);
```
```twig
{% do map.zoom(level) %}
```
```php
$map->zoom($level);
```
:::

Change the map's zoom level.

#### Arguments

- `level` (_int_) - The new zoom level. Must be an integer between `1` - `22`.
 
:::tip Zoom Level Reference
- `1` is extremely zoomed out, a view of the entire planet.
- `22` is extremely zoomed in, as close to the ground as possible.
:::

## `center(coords)`

:::code
```js
map.center(coords);
```
```twig
{% do map.center(coords) %}
```
```php
$map->center($coords);
```
:::

Re-center the map.

#### Arguments

- `coords` (_[coords](/models/coordinates/)_) - New center point of map.

## `fit()`

:::code
```js
map.fit();
```
```twig
{% do map.fit() %}
```
```php
$map->fit();
```
:::

Zoom map to automatically fit all markers within the viewing area. Internally uses [`fitBounds`](https://developers.google.com/maps/documentation/javascript/reference/map#Map.fitBounds).

## `refresh()`

:::code
```js
map.refresh();
```
```twig
{% do map.refresh() %}
```
```php
$map->refresh();
```
:::

Refresh an existing map. You may need to do this after the page has been resized, or if something has been moved or changed.

---
---

### Marker ID formula

The remaining methods all refer to a `markerId` value.

:::warning Formula 
The default formula for a `markerId` is as follows:

```js
    '[ELEMENT ID]-[FIELD HANDLE]' // eg: '101-myAddressField'
```
:::

## `panToMarker(markerId)`

:::code
```js
map.panToMarker(markerId);
```
```twig
{% do map.panToMarker(markerId) %}
```
```php
$map->panToMarker($markerId);
```
:::

Re-center map on the specified marker.

#### Arguments

- `markerId` (_string_) - The ID of the marker that you want to pan to.

## `setMarkerIcon(markerId, icon)`

:::code
```js
map.setMarkerIcon(markerId, icon);
```
```twig
{% do map.setMarkerIcon(markerId, icon) %}
```
```php
$map->setMarkerIcon($markerId, $icon);
```
:::

Set the icon of an existing marker. Internally uses [`setIcon`](https://developers.google.com/maps/documentation/javascript/reference/marker#Marker.setIcon).

#### Arguments

- `markerId` (_string_|_array_|`'*'`) - A marker ID, array of marker IDs, or `'*'` for all markers.
- `icon` (_string_|_[icon](https://developers.google.com/maps/documentation/javascript/reference/marker#Marker.setIcon)_) - The icon to set on the specified marker.

## `hideMarker(markerId)`

:::code
```js
map.hideMarker(markerId);
```
```twig
{% do map.hideMarker(markerId) %}
```
```php
$map->hideMarker($markerId);
```
:::

Hide a marker. The marker will not be destroyed, it will simply be detached from the map.

#### Arguments

- `markerId` (_string_|_array_|`'*'`) - A marker ID, array of marker IDs, or `'*'` for all markers.

## `showMarker(markerId)`

:::code
```js
map.showMarker(markerId);
```
```twig
{% do map.showMarker(markerId) %}
```
```php
$map->showMarker($markerId);
```
:::

Show a marker. The marker will be re-attached to the map.

#### Arguments

- `markerId` (_string_|_array_|`'*'`) - A marker ID, array of marker IDs, or `'*'` for all markers.

## `openInfoWindow(markerId)`

:::code
```js
map.openInfoWindow(markerId);
```
```twig
{% do map.openInfoWindow(markerId) %}
```
```php
$map->openInfoWindow($markerId);
```
:::

Open the info window of a specific marker.

#### Arguments

- `markerId` (_string_|_array_|`'*'`) - A marker ID, array of marker IDs, or `'*'` for all info windows.

## `closeInfoWindow(markerId)`

:::code
```js
map.closeInfoWindow(markerId);
```
```twig
{% do map.closeInfoWindow(markerId) %}
```
```php
$map->closeInfoWindow($markerId);
```
:::

Close the info window of a specific marker.

#### Arguments

- `markerId` (_string_|_array_|`'*'`) - A marker ID, array of marker IDs, or `'*'` for all info windows.

## `hideCircle(circleId)`

:::code
```js
map.hideCircle(circleId);
```
```twig
{% do map.hideCircle(circleId) %}
```
```php
$map->hideCircle($circleId);
```
:::

Hide a circle. The circle will not be destroyed, it will simply be detached from the map.

#### Arguments

- `circleId` (_string_|_array_|`'*'`) - A circle ID, array of circle IDs, or `'*'` for all circles.

## `showCircle(circleId)`

:::code
```js
map.showCircle(circleId);
```
```twig
{% do map.showCircle(circleId) %}
```
```php
$map->showCircle($circleId);
```
:::

Show a circle. The circle will be re-attached to the map.

#### Arguments

- `circleId` (_string_|_array_|`'*'`) - A circle ID, array of circle IDs, or `'*'` for all circles.

## `hideKml(kmlId)`

:::code
```js
map.hideKml(kmlId);
```
```twig
{% do map.hideKml(kmlId) %}
```
```php
$map->hideKml($kmlId);
```
:::

Hide a KML layer. The KML layer will not be destroyed, it will simply be detached from the map.

#### Arguments

- `kmlId` (_string_|_array_|`'*'`) - A KML layer ID, array of KML layer IDs, or `'*'` for all KML layers.

## `showKml(kmlId)`

:::code
```js
map.showKml(kmlId);
```
```twig
{% do map.showKml(kmlId) %}
```
```php
$map->showKml($kmlId);
```
:::

Show a KML layer. The KML layer will be re-attached to the map.

#### Arguments

- `kmlId` (_string_|_array_|`'*'`) - A KML layer ID, array of KML layer IDs, or `'*'` for all KML layers.
