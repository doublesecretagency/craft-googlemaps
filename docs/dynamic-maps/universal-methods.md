# Universal Methods

The following methods apply equally, whether you are working in JavaScript, Twig or PHP. These methods have nearly identical parameters and behaviors across all three languages.

:::warning The Magic of Chaining
Each of these methods can be [chained together](/dynamic-maps/chaining/) in any order you'd like. Chaining can be a powerful technique, allowing you to build complex maps with ease.
:::

There are also a few language-specific methods to be aware of. In addition to the Universal Methods below, check out the extended documentation for [JavaScript Methods](/dynamic-maps/javascript-methods/) and [Twig & PHP Methods](/dynamic-maps/twig-php-methods/).

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

Places additional markers onto the map.

#### Arguments

 - `locations` (_[locations](/dynamic-maps/locations/)_) - Will be used to create additional markers for the map.
 - `options` (_array_|_null_) - The `options` parameter allows you to configure the markers in greater detail. These options will _only_ apply to the markers created in this method call.

:::warning Available Options
Most, but not all, of these options are available across JavaScript, Twig, and PHP. Please note the few options which are not universally available.
:::
 
| Option               | Available            | Description |
|----------------------|:--------------------:|-------------|
| `id`                 | JavaScript, Twig/PHP | Reference point for each marker. |
| `icon`               | JavaScript, Twig/PHP | An `icon` as defined by [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions.icon). |
| `markerOptions`      | JavaScript, Twig/PHP | Accepts any [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions) properties. |
| `infoWindowOptions`  | JavaScript, Twig/PHP | Accepts any [`google.maps.InfoWindowOptions`](https://developers.google.com/maps/documentation/javascript/reference/info-window#InfoWindowOptions) properties. |
| `infoWindowTemplate` | Twig/PHP             | Template path to use for creating [info windows](/dynamic-maps/info-windows/). |
| `field`              | Twig/PHP             | Address field(s) to be included on the map. (includes all by default) |

:::tip Additional Options Details
For more info, please consult either the [JavaScript model](/models/javascript/#markers-locations-options) or the [Dynamic Map model](/models/dynamic-map-model/#markers-locations-options).
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

 - `url` (_string_) - The URL of a given KML layer. It **must** be hosted remotely. The KML file will not work if hosted locally.
 - `options` (_array_|_null_) - The `options` parameter allows you to configure this KML layer in greater detail.

| Option             | Available            | Description |
|--------------------|:--------------------:|-------------|
| `id`               | JavaScript, Twig/PHP | Reference point for each KML layer. |
| `KmlLayerOptions`  | JavaScript, Twig/PHP | Accepts any [`google.maps.KmlLayerOptions`](https://developers.google.com/maps/documentation/javascript/reference/kml#KmlLayerOptions) properties. |

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

 - `styleSet` (_array_) - A collection of styles, most likely generated elsewhere. See the [Styling a Map](/guides/styling-a-map/) guide for more information.

## `zoom(level)`

```js
map.zoom(10);
```

Change the map's zoom level.

#### Arguments

 - `level` (_int_) - The new zoom level. Must be an integer between `1` - `22`.
 
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

#### Arguments

 - `coords` (_[coords](/models/coordinates/)_) - New center point of map.

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

#### Arguments

 - `markerId` (_string_) - The ID of the marker that you want to pan to.

## `setMarkerIcon(markerId, icon)`

```js
map.setMarkerIcon('33-address', 'http://maps.google.com/mapfiles/ms/micons/green.png');
```

Set the icon of an existing marker. Internally uses [`setIcon`](https://developers.google.com/maps/documentation/javascript/reference/marker#Marker.setIcon).

#### Arguments

 - `markerId` (_string_) - The ID of the marker that you want to set the icon for.
 - `icon` (_string_|_[icon](https://developers.google.com/maps/documentation/javascript/reference/marker#Marker.setIcon)_) - The icon to set on the specified marker.

## `hideMarker(markerId)`

```js
map.hideMarker('33-address');
```

Hide a marker. The marker will not be destroyed, it will simply be detached from the map.

#### Arguments

 - `markerId` (_string_) - The ID of the marker that you want to hide.

## `showMarker(markerId)`

```js
map.showMarker('33-address');
```

Show a marker. The marker will be re-attached to the map.

#### Arguments

 - `markerId` (_string_) - The ID of the marker that you want to show.
