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

- `url` (_string_) - The URL of a KML or KMZ file. Relative and local URLs work fine. A file on another domain must allow [cross-origin requests](/guides/kml-layers/#files-on-another-domain).
- `options` (_array_|_null_) - The `options` parameter allows you to configure this KML layer in greater detail.

| Option                | Available            | Description |
|-----------------------|:--------------------:|-------------|
| `id`                  | JavaScript, Twig/PHP | Reference point for each KML layer. |
| `preserveViewport`    | JavaScript, Twig/PHP | If `true`, the map will not zoom to fit the layer once it loads. |
| `suppressInfoWindows` | JavaScript, Twig/PHP | If `true`, clicking a shape will not open an info window. |
| `clickable`           | JavaScript, Twig/PHP | If `false`, the layer will ignore clicks entirely. |
| `zIndex`              | JavaScript, Twig/PHP | Stacking order of the layer, relative to other layers. |
| `style`               | JavaScript, Twig/PHP | Any [`google.maps.Data.StyleOptions`](https://developers.google.com/maps/documentation/javascript/reference/data#Data.StyleOptions) properties. Overrides the styles from the KML file. |
| `kmlLayerOptions`     | JavaScript, Twig/PHP | Any of the options above, nested. If an option is set both ways, the nested value wins. |

:::warning ID required to dynamically adjust KML layers
In order to [further manipulate](/guides/kml-layers/#further-manipulating-kml-layers) a KML layer, you must provide an `id` option value.
:::

For more information, see the [KML Layers](/guides/kml-layers/) guide.

## `geoJson(source, options)`

:::code
```js
map.geoJson(source, options);
```
```twig
{% do map.geoJson(source, options) %}
```
```php
$map->geoJson($source, $options);
```
:::

Applies a GeoJSON layer to the map.

#### Arguments

- `source` (_string_|_array_) - The URL of a GeoJSON file, a JSON string, or the GeoJSON data itself.
- `options` (_array_|_null_) - The `options` parameter allows you to configure this GeoJSON layer in greater detail.

| Option                | Available            | Description |
|-----------------------|:--------------------:|-------------|
| `id`                  | JavaScript, Twig/PHP | Reference point for each GeoJSON layer. |
| `preserveViewport`    | JavaScript, Twig/PHP | If `true`, the map will not zoom to fit the layer once it loads. |
| `suppressInfoWindows` | JavaScript, Twig/PHP | If `true`, clicking a shape will not open an info window. |
| `clickable`           | JavaScript, Twig/PHP | If `false`, the layer will ignore clicks entirely. |
| `zIndex`              | JavaScript, Twig/PHP | Stacking order of the layer, relative to other layers. |
| `style`               | JavaScript, Twig/PHP | Any [`google.maps.Data.StyleOptions`](https://developers.google.com/maps/documentation/javascript/reference/data#Data.StyleOptions) properties. Overrides the styles from the GeoJSON data. |

For more information, see the [GeoJSON Layers](/guides/geojson-layers/) guide.

## `gpx(url, options)`

:::code
```js
map.gpx(url, options);
```
```twig
{% do map.gpx(url, options) %}
```
```php
$map->gpx($url, $options);
```
:::

Applies a GPX layer (tracks, routes, and waypoints) to the map.

#### Arguments

- `url` (_string_) - The URL of a GPX file. Relative and local URLs work fine. A file on another domain must allow [cross-origin requests](/guides/kml-layers/#files-on-another-domain).
- `options` (_array_|_null_) - The `options` parameter allows you to configure this GPX layer in greater detail.

| Option                | Available            | Description |
|-----------------------|:--------------------:|-------------|
| `id`                  | JavaScript, Twig/PHP | Reference point for each GPX layer. |
| `preserveViewport`    | JavaScript, Twig/PHP | If `true`, the map will not zoom to fit the layer once it loads. |
| `suppressInfoWindows` | JavaScript, Twig/PHP | If `true`, clicking a track or waypoint will not open an info window. |
| `clickable`           | JavaScript, Twig/PHP | If `false`, the layer will ignore clicks entirely. |
| `zIndex`              | JavaScript, Twig/PHP | Stacking order of the layer, relative to other layers. |
| `style`               | JavaScript, Twig/PHP | Any [`google.maps.Data.StyleOptions`](https://developers.google.com/maps/documentation/javascript/reference/data#Data.StyleOptions) properties. |

For more information, see the [GPX Tracks](/guides/gpx-tracks/) guide.

## `groundOverlay(url, bounds, options)`

:::code
```js
map.groundOverlay(url, bounds, options);
```
```twig
{% do map.groundOverlay(url, bounds, options) %}
```
```php
$map->groundOverlay($url, $bounds, $options);
```
:::

Places an image onto the map, stretched to fit the specified bounds.

#### Arguments

- `url` (_string_) - The URL of the image.
- `bounds` (_array_) - The edges of the image, as `north`, `south`, `east`, and `west` values.
- `options` (_array_|_null_) - The `options` parameter allows you to configure this ground overlay in greater detail.

| Option      | Available            | Description |
|-------------|:--------------------:|-------------|
| `id`        | JavaScript, Twig/PHP | Reference point for each ground overlay. |
| `opacity`   | JavaScript, Twig/PHP | Opacity of the image, from `0` (invisible) to `1` (solid). |
| `clickable` | JavaScript, Twig/PHP | If `false`, the image will ignore clicks. |

For more information, see the [Ground Overlays](/guides/ground-overlays/) guide.

## `dataset(datasetId, options)`

:::code
```js
map.dataset(datasetId, options);
```
```twig
{% do map.dataset(datasetId, options) %}
```
```php
$map->dataset($datasetId, $options);
```
:::

Shows a dataset which has been uploaded to Google Cloud. Requires a vector Map ID.

#### Arguments

- `datasetId` (_string_) - The ID of the dataset in Google Cloud.
- `options` (_array_|_null_) - The `options` parameter allows you to configure this dataset in greater detail.

| Option                | Available            | Description |
|-----------------------|:--------------------:|-------------|
| `id`                  | JavaScript, Twig/PHP | Reference point for the dataset. Defaults to the dataset ID. |
| `style`               | JavaScript, Twig/PHP | Any [`google.maps.FeatureStyleOptions`](https://developers.google.com/maps/documentation/javascript/reference/data-driven-styling#FeatureStyleOptions) properties. |
| `suppressInfoWindows` | JavaScript, Twig/PHP | If `true`, clicking a shape will not open an info window. |

For more information, see the [Datasets & Boundaries](/guides/datasets-and-boundaries/) guide.

## `boundaries(featureType, placeIds, options)`

:::code
```js
map.boundaries(featureType, placeIds, options);
```
```twig
{% do map.boundaries(featureType, placeIds, options) %}
```
```php
$map->boundaries($featureType, $placeIds, $options);
```
:::

Shades Google's own boundaries of countries, states, cities, and more. Requires a vector Map ID.

#### Arguments

- `featureType` (_string_) - The type of boundary. One of `COUNTRY`, `ADMINISTRATIVE_AREA_LEVEL_1`, `ADMINISTRATIVE_AREA_LEVEL_2`, `LOCALITY`, `POSTAL_CODE`, or `SCHOOL_DISTRICT`.
- `placeIds` (_string_|_array_) - A Place ID, or array of Place IDs, of the areas to shade.
- `options` (_array_|_null_) - The `options` parameter allows you to configure these boundaries in greater detail.

| Option  | Available            | Description |
|---------|:--------------------:|-------------|
| `id`    | JavaScript, Twig/PHP | Reference point for the boundaries. Defaults to the feature type. |
| `style` | JavaScript, Twig/PHP | Any [`google.maps.FeatureStyleOptions`](https://developers.google.com/maps/documentation/javascript/reference/data-driven-styling#FeatureStyleOptions) properties. |

For more information, see the [Datasets & Boundaries](/guides/datasets-and-boundaries/) guide.

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

Zoom map to automatically fit all markers and layers within the viewing area. Internally uses [`fitBounds`](https://developers.google.com/maps/documentation/javascript/reference/map#Map.fitBounds).

:::tip Which layers are included?
KML, GeoJSON, and GPX layers and ground overlays are included, as long as they are visible and have finished loading. Datasets and boundaries are left out, since Google doesn't provide their bounds.
:::

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

## `hideGeoJson(geoJsonId)`

:::code
```js
map.hideGeoJson(geoJsonId);
```
```twig
{% do map.hideGeoJson(geoJsonId) %}
```
```php
$map->hideGeoJson($geoJsonId);
```
:::

Hide a GeoJSON layer. The GeoJSON layer will not be destroyed, it will simply be detached from the map.

#### Arguments

- `geoJsonId` (_string_|_array_|`'*'`) - A GeoJSON layer ID, array of GeoJSON layer IDs, or `'*'` for all GeoJSON layers.

## `showGeoJson(geoJsonId)`

:::code
```js
map.showGeoJson(geoJsonId);
```
```twig
{% do map.showGeoJson(geoJsonId) %}
```
```php
$map->showGeoJson($geoJsonId);
```
:::

Show a GeoJSON layer. The GeoJSON layer will be re-attached to the map.

#### Arguments

- `geoJsonId` (_string_|_array_|`'*'`) - A GeoJSON layer ID, array of GeoJSON layer IDs, or `'*'` for all GeoJSON layers.

## `hideGpx(gpxId)`

:::code
```js
map.hideGpx(gpxId);
```
```twig
{% do map.hideGpx(gpxId) %}
```
```php
$map->hideGpx($gpxId);
```
:::

Hide a GPX layer. The GPX layer will not be destroyed, it will simply be detached from the map.

#### Arguments

- `gpxId` (_string_|_array_|`'*'`) - A GPX layer ID, array of GPX layer IDs, or `'*'` for all GPX layers.

## `showGpx(gpxId)`

:::code
```js
map.showGpx(gpxId);
```
```twig
{% do map.showGpx(gpxId) %}
```
```php
$map->showGpx($gpxId);
```
:::

Show a GPX layer. The GPX layer will be re-attached to the map.

#### Arguments

- `gpxId` (_string_|_array_|`'*'`) - A GPX layer ID, array of GPX layer IDs, or `'*'` for all GPX layers.

## `hideGroundOverlay(overlayId)`

:::code
```js
map.hideGroundOverlay(overlayId);
```
```twig
{% do map.hideGroundOverlay(overlayId) %}
```
```php
$map->hideGroundOverlay($overlayId);
```
:::

Hide a ground overlay. The ground overlay will not be destroyed, it will simply be detached from the map.

#### Arguments

- `overlayId` (_string_|_array_|`'*'`) - A ground overlay ID, array of ground overlay IDs, or `'*'` for all ground overlays.

## `showGroundOverlay(overlayId)`

:::code
```js
map.showGroundOverlay(overlayId);
```
```twig
{% do map.showGroundOverlay(overlayId) %}
```
```php
$map->showGroundOverlay($overlayId);
```
:::

Show a ground overlay. The ground overlay will be re-attached to the map.

#### Arguments

- `overlayId` (_string_|_array_|`'*'`) - A ground overlay ID, array of ground overlay IDs, or `'*'` for all ground overlays.

## `hideDataset(datasetId)`

:::code
```js
map.hideDataset(datasetId);
```
```twig
{% do map.hideDataset(datasetId) %}
```
```php
$map->hideDataset($datasetId);
```
:::

Hide a dataset. The dataset will not be destroyed, it will simply be hidden from view.

#### Arguments

- `datasetId` (_string_|_array_|`'*'`) - A dataset ID, array of dataset IDs, or `'*'` for all datasets.

## `showDataset(datasetId)`

:::code
```js
map.showDataset(datasetId);
```
```twig
{% do map.showDataset(datasetId) %}
```
```php
$map->showDataset($datasetId);
```
:::

Show a dataset. The dataset will be shown again.

#### Arguments

- `datasetId` (_string_|_array_|`'*'`) - A dataset ID, array of dataset IDs, or `'*'` for all datasets.

## `hideBoundaries(boundariesId)`

:::code
```js
map.hideBoundaries(boundariesId);
```
```twig
{% do map.hideBoundaries(boundariesId) %}
```
```php
$map->hideBoundaries($boundariesId);
```
:::

Hide boundaries. The boundaries will not be destroyed, they will simply be hidden from view.

#### Arguments

- `boundariesId` (_string_|_array_|`'*'`) - A boundaries ID, array of boundaries IDs, or `'*'` for all boundaries.

## `showBoundaries(boundariesId)`

:::code
```js
map.showBoundaries(boundariesId);
```
```twig
{% do map.showBoundaries(boundariesId) %}
```
```php
$map->showBoundaries($boundariesId);
```
:::

Show boundaries. The boundaries will be shown again.

#### Arguments

- `boundariesId` (_string_|_array_|`'*'`) - A boundaries ID, array of boundaries IDs, or `'*'` for all boundaries.
