---
description:
---

# Dynamic Map Model

The Dynamic Map Model is critical for generating a [Dynamic Map](/dynamic-maps/). Thanks to the magic of [chaining](/dynamic-maps/chaining/), it allows you to build maps that are as complex as they need to be.

## Public Properties

### `id`

_string_ - The map's unique ID. Can be set manually or generated automatically.

## Public Methods

### `__construct($locations = [], $options = [])`

This method will be called when a `new DynamicMap` is initialized. It creates a starting point which sets the map-building chain in motion. You will be able to build upon the map by adding markers, KML layers, etc.

:::code
```twig
{% set map = googleMaps.map(locations) %}
```
```php
$map = GoogleMaps::map($locations);
```
:::

:::warning The "map" variable
For each of the remaining examples on this page, the `map` variable will be an instance of a **Dynamic Map Model**. In each example, you will see how map methods can be chained at will.

It will be assumed that the `map` object has already been initialized, as demonstrated above.
:::

Once you have the map object in hand, you can then chain other methods to further customize the map. There is no limit as to how many methods you can chain, nor what order they should be in.

#### Arguments

- `$locations` (_mixed_) - See a description of acceptable [locations...](/dynamic-maps/locations/)
- `$options` (_array_) - Optional parameters to configure the map. (see below)

### Dynamic Map Options

| Option               | Type              | Default | Description
|:---------------------|:-----------------:|:-------:|:------------
| `id`                 | _string_          | <span style="white-space:nowrap">`"map-{random}"`</span> | Set the `id` attribute of the map container.
| `width`              | _int_             | _null_  | Set the width of the map (in px).
| `height`             | _int_             | _null_  | Set the height of the map (in px).
| `zoom`               | _int_             | <span style="white-space:nowrap">via `fitBounds`</span> | Set the default zoom level of the map. <span style="white-space:nowrap">(`1`-`22`)</span>
| `center`             | [coords](/models/coordinates/) | <span style="white-space:nowrap">via `fitBounds`</span> | Set the center position of the map.
| `styles`             | _array_           | _null_  | An array of [map styles](/guides/styling-a-map/).
| `cluster`            | _bool_\|_array_   | _false_ | Enable [marker clustering](/dynamic-maps/clustering-markers/).
| `mapOptions`         | _object_          | _null_  | Accepts any [`google.maps.MapOptions`](https://developers.google.com/maps/documentation/javascript/reference/map#MapOptions) properties.
| `markerOptions`      | _object_          | _null_  | Accepts any [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions) properties.
| `infoWindowOptions`  | _object_          | _null_  | Accepts any [`google.maps.InfoWindowOptions`](https://developers.google.com/maps/documentation/javascript/reference/info-window#InfoWindowOptions) properties.
| `infoWindowTemplate` | _string_          | _null_  | Template path to use for creating [info windows](/dynamic-maps/info-windows/).
| `infoWindowData`     | _array_           | _null_  | Additional data for passing into an [info window](/dynamic-maps/info-windows/).
| `markerLink`         | _string_          | _null_  | URL to go to when each marker is clicked.
| `markerClick`        | _string_          | _null_  | JS callback function triggered when each marker is clicked.
| `field`              | _string_\|_array_ | _null_  | Address field(s) to be included on the map. (includes all by default)

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::tip Locations are Skippable
If you skip the `locations` parameter, a blank map will be created.
:::

---
---

### `markers($locations, $options = [])`

Append markers to an existing map object.

#### Arguments

- `$locations` (_mixed_) - See a description of acceptable [locations...](/dynamic-maps/locations/)
- `$options` (_array_) - Optional parameters to configure the markers. (see below)
 
| Option               | Type               | Default | Description
|:---------------------|:------------------:|:-------:|:------------
| `id`                 | _string_           | <span style="white-space:nowrap">`"marker-{random}"`</span> | Reference point for each marker.
| `icon`               | _object_\|_string_ | _null_  | An `icon` as defined by [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions.icon).
| `markerOptions`      | _object_           | _null_  | Accepts any [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions) properties.
| `infoWindowOptions`  | _object_           | _null_  | Accepts any [`google.maps.InfoWindowOptions`](https://developers.google.com/maps/documentation/javascript/reference/info-window#InfoWindowOptions) properties.
| `infoWindowTemplate` | _string_           | _null_  | Template path to use for creating [info windows](/dynamic-maps/info-windows/).
| `infoWindowData`     | _array_            | _null_  | Additional data for passing into an [info window](/dynamic-maps/info-windows/).
| `markerLink`         | _string_           | _null_  | URL to go to when each marker is clicked.
| `markerClick`        | _string_           | _null_  | JS callback function triggered when each marker is clicked.
| `field`              | _string_\|_array_  | _null_  | Address field(s) to be included on the map. (includes all by default)

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.markers(locations) %}
```
```php
$map->markers($locations);
```
:::

---
---

### `circles($locations, $options = [])`

Add one or more circles to an existing map object.

#### Arguments

- `$locations` (_mixed_) - See a description of acceptable [locations...](/dynamic-maps/locations/)
- `$options` (_array_) - Optional parameters to configure circles. (see below)

| Option          | Type     | Default | Description              
|:----------------|:--------:|:-------:|:-------------
| `id`            | _string_ | <span style="white-space:nowrap">`"circle-{random}"`</span> | Reference point for each circle.
| `circleOptions` | _object_ | _null_  | Accepts any [`google.maps.CircleOptions`](https://developers.google.com/maps/documentation/javascript/reference/polygon#CircleOptions) properties.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.circles(locations) %}
```
```php
$map->circles($locations);
```
:::

---
---

### `kml($files, $options = [])`

Append one or more KML layers to an existing map object.

#### Arguments

- `$files` (_mixed_)
- `$options` (_array_) - Optional parameters to configure the KML layers. (see below)
 
| Option             | Type     | Default | Description |
|:-------------------|:--------:|:-------:|:------------|
| `id`               | _string_ | <span style="white-space:nowrap">`"kml-{random}"`</span> | Reference point for each KML layer. |
| `kmlLayerOptions`  | _object_ | _null_  | Accepts any [`google.maps.KmlLayerOptions`](https://developers.google.com/maps/documentation/javascript/reference/kml#KmlLayerOptions) properties. |

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.kml(files) %}
```
```php
$map->kml($files);
```
:::

---
---

### `styles($styleSet)`

Style a map based on a given array of styles.

:::warning Generating Styles
For more information on how to generate a set of styles, read [Styling a Map](/guides/styling-a-map/).
:::

#### Arguments

- `$styleSet` (_array_) - A set of styles to be applied to the map.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.styles(styleSet) %}
```
```php
$map->styles($styleSet);
```
:::

---
---

### `zoom($level)`

Change the map's zoom level.

#### Arguments

- `$level` (_string_) - The new zoom level. Must be an integer between `1` - `22`.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.zoom(level) %}
```
```php
$map->zoom($level);
```
:::

---
---

### `center($coords)`

Re-center the map.

#### Arguments

- `$coords` (_[coords](/models/coordinates/)_) - A simple key-value set of [coordinates](/models/coordinates/).

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.center(coords) %}
```
```php
$map->center($coords);
```
:::

---
---

### `fit()`

Zoom map to automatically fit all markers within the viewing area.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.fit() %}
```
```php
$map->fit();
```
:::

---
---

### `refresh()`

Refresh an existing map. You may need to do this after the page has been resized, or if something has been moved or changed.

:::tip Generally Useless
There probably aren't a lot of good reasons to use this method in Twig or PHP, because it would be fired immediately after the map finishes rendering. It exists mainly for parity between languages.
:::

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.refresh() %}
```
```php
$map->refresh();
```
:::

---
---

### `panToMarker($markerId)`

Re-center map on the specified marker.

#### Arguments

- `$markerId` (_string_) - ID of the target marker.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.panToMarker(markerId) %}
```
```php
$map->panToMarker($markerId);
```
:::

---
---

### `setMarkerIcon($markerId, $icon)`

Set the icon of an existing marker.

#### Arguments

- `$markerId` (_string_|_array_|`'*'`) - A marker ID, array of marker IDs, or `'*'` for all markers.
- `$icon` (_string_|_[icon](https://developers.google.com/maps/documentation/javascript/reference/marker#Marker.setIcon)_) - URL of marker icon image.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.setMarkerIcon(markerId, icon) %}
```
```php
$map->setMarkerIcon($markerId, $icon);
```
:::

---
---

### `hideMarker($markerId)`

Hide a marker.

#### Arguments

- `$markerId` (_string_|_array_|`'*'`) - A marker ID, array of marker IDs, or `'*'` for all markers.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.hideMarker(markerId) %}
```
```php
$map->hideMarker($markerId);
```
:::

---
---

### `showMarker($markerId)`

Show a marker.

#### Arguments

- `$markerId` (_string_|_array_|`'*'`) - A marker ID, array of marker IDs, or `'*'` for all markers.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.showMarker(markerId) %}
```
```php
$map->showMarker($markerId);
```
:::

---
---

### `openInfoWindow($markerId)`

Open the info window of a specific marker.

#### Arguments

- `$markerId` (_string_|_array_|`'*'`) - A marker ID, array of marker IDs, or `'*'` for all info windows.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.openInfoWindow(markerId) %}
```
```php
$map->openInfoWindow($markerId);
```
:::

---
---

### `closeInfoWindow($markerId)`

Close the info window of a specific marker.

#### Arguments

- `$markerId` (_string_|_array_|`'*'`) - A marker ID, array of marker IDs, or `'*'` for all info windows.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.closeInfoWindow(markerId) %}
```
```php
$map->closeInfoWindow($markerId);
```
:::

---
---

### `hideCircle($circleId)`

Hide a circle.

#### Arguments

- `$circleId` (_string_|_array_|`'*'`) - A circle ID, array of circle IDs, or `'*'` for all circles.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.hideCircle(circleId) %}
```
```php
$map->hideCircle($circleId);
```
:::

---
---

### `showCircle($circleId)`

Show a circle.

#### Arguments

- `$circleId` (_string_|_array_|`'*'`) - A circle ID, array of circle IDs, or `'*'` for all circles.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.showCircle(circleId) %}
```
```php
$map->showCircle($circleId);
```
:::

---
---

### `hideKml($kmlId)`

Hide a KML layer.

#### Arguments

- `$kmlId` (_string_|_array_|`'*'`) - A KML layer ID, array of KML layer IDs, or `'*'` for all KML layers.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.hideKml(kmlId) %}
```
```php
$map->hideKml($kmlId);
```
:::

---
---

### `showKml($kmlId)`

Show a KML layer.

#### Arguments

- `$kmlId` (_string_|_array_|`'*'`) - A KML layer ID, array of KML layer IDs, or `'*'` for all KML layers.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

:::code
```twig
{% do map.showKml(kmlId) %}
```
```php
$map->showKml($kmlId);
```
:::

---
---

### `tag($options = [])`

Renders the necessary `<div>` container to hold the map. The final `<div>` will contain specific attributes and data, which are then used to generate the map. Each container must be **initialized** in order for its dynamic map to be created.

#### Arguments

- `$options` (_array_) - Configuration options for the rendered `<div>`.

| Option     | Type     | Default | Description
|:-----------|:--------:|:-------:|:------------
| `init`     | _bool_   | `true`  | Whether to automatically initialize the map via JavaScript.
| `assets`   | _bool_   | `true`  | Whether to preload the necessary JavaScript assets.
| `callback` | _string_ | `null`  | JavaScript function to run after the map has loaded.
| `params`   | _object_ | `{}`    | Optional [parameters](https://developers.google.com/maps/documentation/javascript/url-params) for the Google Maps API.
| `api`      | _object_ | `{}`    | _[DEPRECATED]_ Use `params` instead.

By setting the `init` option to `false`, the map will not be automatically initialized in JavaScript. It must therefore be [manually initialized in JavaScript](/dynamic-maps/javascript-methods/#init-mapid-null-callback-null) when the page has completely rendered.

#### Returns

- _Markup_ - A Twig Markup instance, ready to be rendered in Twig with curly braces.

:::code
```twig
{{ map.tag() }}
```
```php
$twigMarkup = $map->tag();
```
:::

If you need to [disable the automatic map initialization](/guides/delay-map-init/):

:::code
```twig
{{ map.tag({'init': false}) }}
```
```php
$twigMarkup = $map->tag(['init' => false]);
```
:::

If you need to [change the map language and/or region](/guides/changing-map-language/):

:::code
```twig
{{ map.tag({
    'params': {
        'language': 'ja',
        'region': 'JP'
    }
}) }}
```
```php
$twigMarkup = $map->tag([
    'params' => [
        'language' => 'ja',
        'region' => 'JP'
    ]
]);
```
:::

---
---

### `getDna()`

Get the complete map DNA, which is used to hydrate a map's container in the DOM.

_Aliased as `dna` property via magic method._

#### Returns

- _array_ - An array of data containing the complete map details.

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
