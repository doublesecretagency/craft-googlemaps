---
description:
---

# `dynamicmap.js`

This file contains the `DynamicMap` model, which can be used to create dynamic, chainable map objects. Each instance of a `DynamicMap` object correlates with a different Google Map instance.

:::warning Don't access directly
It is extremely rare to need to create a `DynamicMap` model directly. You will almost always use the [`googleMaps` singleton object](/javascript/googlemaps.js/) to create and retrieve map objects.
:::

For a more comprehensive explanation of how to use the internal API, check out the docs regarding the [Universal Methods](/dynamic-maps/universal-methods/) and [JavaScript Methods](/dynamic-maps/javascript-methods/).

### The `map` variable

For each example on this page, a `map` variable will be an instance of a specific `DynamicMap` object. Each example assumes that the `map` object has already been initialized, as demonstrated on the [`googlemaps.js` page](/javascript/googlemaps.js/).

:::tip Get a Map
A `map` can be _created_ using `googleMaps.map`, or _retrieved_ using `googleMaps.getMap`.
:::

## Map Methods

### `markers(locations, options = [])`

Add markers to an existing map. Does not overwrite any existing markers.

```js
map.markers([
    {'lat': 40.730610, 'lng': -73.935242},  // New York
    {'lat': 34.052235, 'lng': -118.243683}, // Los Angeles
    {'lat': 41.881832, 'lng': -87.623177}   // Chicago
]);
```

#### Arguments

- `locations` (_[coords](/models/coordinates/)_|_array_) - See a description of acceptable [locations...](/dynamic-maps/locations/)
- `options` (_array_) - Optional parameters to configure the markers. (see below)
 
| Option               | Type                 | Default | Description
|----------------------|:--------------------:|:-------:|-------------
| `id`                 | _string_             | <span style="white-space:nowrap">`"marker-{random}"`</span> | Reference point for each marker.
| `icon`               | _object_ or _string_ | _null_  | An `icon` as defined by [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions.icon).
| `markerOptions`      | _object_             | _null_  | Accepts any [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions) properties.
| `infoWindowOptions`  | _object_             | _null_  | Accepts any [`google.maps.InfoWindowOptions`](https://developers.google.com/maps/documentation/javascript/reference/info-window#InfoWindowOptions) properties.
| `markerLink`         | _string_             | _null_  | URL to go to when marker is clicked.
| `markerClick`        | _function_           | _null_  | JS callback function triggered when marker is clicked.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `circles(locations, options = [])`

Add one or more circles to an existing map.

#### Arguments

- `locations` (_[coords](/models/coordinates/)_|_array_) - See a description of acceptable [locations...](/dynamic-maps/locations/)
- `options` (_array_) - Optional parameters to configure circles. (see below)

| Option | Type     |                           Default                           | Description
|--------|:--------:|:-----------------------------------------------------------:|-------------
| `id`   | _string_ | <span style="white-space:nowrap">`"circle-{random}"`</span> | Reference point for each circle.
| `circleOptions` | _object_ | _null_ | Accepts any [`google.maps.CircleOptions`](https://developers.google.com/maps/documentation/javascript/reference/polygon#CircleOptions) properties.

```js
const location = {'lat': 40.730610, 'lng': -73.935242};
const options = {'circleOptions': {
    strokeColor: "#226666",
    strokeOpacity: 0.8,
    strokeWeight: 2,
    fillColor: "#226666",
    fillOpacity: 0.35,
    radius: 100000
}};

map.circles(location, options);
```

:::tip How to Draw Circles
For a more elaborate explanation, see the guide for [Drawing Circles...](/guides/drawing-circles/)
:::

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `kml(url, options = [])`

Append a KML layer to an existing map object.

#### Arguments

- `url` (_string_) - URL of the KML or KMZ file.
- `options` (_array_) - Optional parameters to configure the KML layer. (see below)
 
| Option                | Type                 | Default | Description
|-----------------------|:--------------------:|:-------:|-------------
| `id`                  | _string_             | <span style="white-space:nowrap">`"kml-{random}"`</span> | Reference point for each KML layer.
| `preserveViewport`    | _bool_               | `false` | If `true`, the map will not zoom to fit the layer once it loads.
| `suppressInfoWindows` | _bool_               | `false` | If `true`, clicking a shape will not open an info window.
| `clickable`           | _bool_               | `true`  | If `false`, the layer will ignore clicks entirely.
| `zIndex`              | _int_                | _null_  | Stacking order of the layer, relative to other layers.
| `style`               | _object_\|_function_ | _null_  | Any [`google.maps.Data.StyleOptions`](https://developers.google.com/maps/documentation/javascript/reference/data#Data.StyleOptions) properties, or a [styling function](https://developers.google.com/maps/documentation/javascript/reference/data#Data.StylingFunction). Overrides the styles from the KML file.
| `kmlLayerOptions`     | _object_             | _null_  | Any of the options above, nested. If an option is set both ways, the nested value wins.

```js
map.kml('https://googlearchive.github.io/js-v2-samples/ggeoxml/cta.kml');
```

:::tip Creating a KML file
KML files can be created using [Google My Maps](https://www.google.com/maps/about/mymaps/) or a similar service.
:::

:::warning Files on another domain
A KML file on another domain will only load if that server allows [cross-origin requests](/guides/kml-layers/#files-on-another-domain).
:::

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `geoJson(source, options = [])`

Append a GeoJSON layer to an existing map object.

#### Arguments

- `source` (_string_|_object_) - URL of a GeoJSON file, a JSON string, or the GeoJSON data itself.
- `options` (_array_) - Optional parameters to configure the GeoJSON layer. (see below)

| Option                | Type                 | Default | Description
|-----------------------|:--------------------:|:-------:|-------------
| `id`                  | _string_             | <span style="white-space:nowrap">`"geojson-{random}"`</span> | Reference point for each GeoJSON layer.
| `preserveViewport`    | _bool_               | `false` | If `true`, the map will not zoom to fit the layer once it loads.
| `suppressInfoWindows` | _bool_               | `false` | If `true`, clicking a shape will not open an info window.
| `clickable`           | _bool_               | `true`  | If `false`, the layer will ignore clicks entirely.
| `zIndex`              | _int_                | _null_  | Stacking order of the layer, relative to other layers.
| `style`               | _object_\|_function_ | _null_  | Any [`google.maps.Data.StyleOptions`](https://developers.google.com/maps/documentation/javascript/reference/data#Data.StyleOptions) properties, or a [styling function](https://developers.google.com/maps/documentation/javascript/reference/data#Data.StylingFunction). Overrides the styles from the GeoJSON data.

```js
map.geoJson('/files/parks.geojson', {'id': 'my-geojson'});
```

:::tip GeoJSON Layers
For a more elaborate explanation, see the guide for [GeoJSON Layers...](/guides/geojson-layers/)
:::

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `gpx(url, options = [])`

Append a GPX layer (tracks, routes, and waypoints) to an existing map object.

#### Arguments

- `url` (_string_) - URL of the GPX file.
- `options` (_array_) - Optional parameters to configure the GPX layer. (see below)

| Option                | Type                 | Default | Description
|-----------------------|:--------------------:|:-------:|-------------
| `id`                  | _string_             | <span style="white-space:nowrap">`"gpx-{random}"`</span> | Reference point for each GPX layer.
| `preserveViewport`    | _bool_               | `false` | If `true`, the map will not zoom to fit the layer once it loads.
| `suppressInfoWindows` | _bool_               | `false` | If `true`, clicking a track or waypoint will not open an info window.
| `clickable`           | _bool_               | `true`  | If `false`, the layer will ignore clicks entirely.
| `zIndex`              | _int_                | _null_  | Stacking order of the layer, relative to other layers.
| `style`               | _object_\|_function_ | _null_  | Any [`google.maps.Data.StyleOptions`](https://developers.google.com/maps/documentation/javascript/reference/data#Data.StyleOptions) properties, or a [styling function](https://developers.google.com/maps/documentation/javascript/reference/data#Data.StylingFunction).

```js
map.gpx('/files/morning-hike.gpx', {'id': 'my-gpx'});
```

:::tip GPX Tracks
For a more elaborate explanation, see the guide for [GPX Tracks...](/guides/gpx-tracks/)
:::

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `groundOverlay(url, bounds, options = [])`

Place an image onto an existing map object, stretched to fit the specified bounds.

#### Arguments

- `url` (_string_) - URL of the image.
- `bounds` (_object_) - The edges of the image, as `north`, `south`, `east`, and `west` values.
- `options` (_array_) - Optional parameters to configure the ground overlay. (see below)

| Option      | Type     | Default | Description
|-------------|:--------:|:-------:|-------------
| `id`        | _string_ | <span style="white-space:nowrap">`"ground-overlay-{random}"`</span> | Reference point for each ground overlay.
| `opacity`   | _float_  | `1`     | Opacity of the image, from `0` (invisible) to `1` (solid).
| `clickable` | _bool_   | `true`  | If `false`, the image will ignore clicks.

```js
map.groundOverlay('/images/historic-bermuda.jpg', {
    'north': 32.3960,
    'south': 32.2460,
    'east': -64.6420,
    'west': -64.8880
}, {'id': 'my-overlay'});
```

:::tip Ground Overlays
For a more elaborate explanation, see the guide for [Ground Overlays...](/guides/ground-overlays/)
:::

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `dataset(datasetId, options = [])`

Show a dataset which has been uploaded to Google Cloud. Requires a vector Map ID.

#### Arguments

- `datasetId` (_string_) - The ID of the dataset in Google Cloud.
- `options` (_array_) - Optional parameters to configure the dataset. (see below)

| Option                | Type     | Default | Description
|-----------------------|:--------:|:-------:|-------------
| `id`                  | _string_ | _(the dataset ID)_ | Reference point for the dataset.
| `style`               | _object_ | _(blue)_ | Any [`google.maps.FeatureStyleOptions`](https://developers.google.com/maps/documentation/javascript/reference/data-driven-styling#FeatureStyleOptions) properties.
| `suppressInfoWindows` | _bool_   | `false` | If `true`, clicking a shape will not open an info window.

```js
map.dataset('YOUR_DATASET_ID', {'id': 'my-dataset'});
```

:::tip Datasets & Boundaries
For a more elaborate explanation, see the guide for [Datasets & Boundaries...](/guides/datasets-and-boundaries/)
:::

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `boundaries(featureType, placeIds, options = [])`

Shade Google's own boundaries of countries, states, cities, and more. Requires a vector Map ID.

#### Arguments

- `featureType` (_string_) - The type of boundary. One of `COUNTRY`, `ADMINISTRATIVE_AREA_LEVEL_1`, `ADMINISTRATIVE_AREA_LEVEL_2`, `LOCALITY`, `POSTAL_CODE`, or `SCHOOL_DISTRICT`.
- `placeIds` (_string_|_array_) - A Place ID, or array of Place IDs, of the areas to shade.
- `options` (_array_) - Optional parameters to configure the boundaries. (see below)

| Option  | Type     | Default | Description
|---------|:--------:|:-------:|-------------
| `id`    | _string_ | _(the feature type)_ | Reference point for the boundaries.
| `style` | _object_ | _(blue)_ | Any [`google.maps.FeatureStyleOptions`](https://developers.google.com/maps/documentation/javascript/reference/data-driven-styling#FeatureStyleOptions) properties.

```js
map.boundaries('ADMINISTRATIVE_AREA_LEVEL_1', placeId, {'id': 'my-boundaries'});
```

:::tip Datasets & Boundaries
For a more elaborate explanation, see the guide for [Datasets & Boundaries...](/guides/datasets-and-boundaries/)
:::

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `styles(styleSet)`

Style a map based on a given collection of styles.

:::tip Generating Styles
For more information on how to generate a set of styles, read [Styling a Map](/guides/styling-a-map/).
:::

#### Arguments

- `styleSet` (_array_) - A set of styles to be applied to the map.

```js
map.styles([
    {
        "featureType": "landscape",
        "stylers": [
            {"color": "#f9ddc5"},
            {"lightness": -7}
        ]
    },
    {
        "featureType": "road",
        "stylers": [
            {"color": "#813033"},
            {"lightness": 43}
        ]
    }
]);
```

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `zoom(level)`

Change the map's zoom level.

#### Arguments

- `level` (_int_) - The new zoom level. Must be an integer between `1` - `22`.

```js
map.zoom(10);
```

:::tip Zoom Level Reference
- `1` is zoomed out, a view of the entire planet.
- `22` is zoomed in, as close to the ground as possible.
:::

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `center(coords)`

Re-center the map.

#### Arguments

- `coords` (_[coords](/models/coordinates/)_) A simple key-value set of coordinates.

```js
map.center({
   'lat': 32.3113966,
   'lng': -64.7527469
});
```

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `fit()`

Zoom and center map to fit all markers and layers within the viewing area. Internally uses [`fitBounds`](https://developers.google.com/maps/documentation/javascript/reference/map#Map.fitBounds).

```js
map.fit();
```

:::tip Which layers are included?
KML, GeoJSON, and GPX layers and ground overlays are included, as long as they are visible and have finished loading. Datasets and boundaries are left out, since Google doesn't provide their bounds.
:::

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `refresh()`

Refresh an existing map. You may need to do this after the page has been resized, or if something has been moved or changed.

```js
map.refresh();
```

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

## Marker Methods
 
:::warning Automatically generated marker IDs
If the marker has been created from an Element, it will have a marker ID matching this formula:

```
    [ELEMENT ID]-[FIELD HANDLE]
```

Let's say you have an Address field with the handle of `address` attached to your Entries. When you use those entries to create a map, the markers will generate IDs similar to this:

```
    21-address
    33-address
    42-address
    etc...
```
:::

Conversely, if the markers have been created manually via JavaScript, it will use the marker ID specified in the options, or even stowed alongside the coordinates.

If no marker ID is specified, new markers will use a randomly generated ID.

---
---

### `panToMarker(markerId)`

Re-center map on the specified marker.

```js
map.panToMarker('33-address');
```

#### Arguments

- `markerId` (_string_) - The ID of the marker that you want to pan to.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `setMarkerIcon(markerId, icon)`

Set the icon of an existing marker. Internally uses [`setIcon`](https://developers.google.com/maps/documentation/javascript/reference/marker#Marker.setIcon).

```js
map.setMarkerIcon('33-address', 'http://maps.google.com/mapfiles/ms/micons/green.png');
```

#### Arguments

- `markerId` (_string_|_array_|`'*'`) - A marker ID, array of marker IDs, or `'*'` for all markers.
- `icon` (_string_|_[icon](https://developers.google.com/maps/documentation/javascript/reference/marker#Marker.setIcon)_) - The icon to set on the specified marker.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `hideMarker(markerId)`

Hide a marker. The marker will not be destroyed, it will simply be detached from the map.

```js
map.hideMarker('33-address');
```

#### Arguments

- `markerId` (_string_|_array_|`'*'`) - A marker ID, array of marker IDs, or `'*'` for all markers.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `showMarker(markerId)`

Show a marker. The marker will be re-attached to the map.

```js
map.showMarker('33-address');
```

#### Arguments

- `markerId` (_string_|_array_|`'*'`) - A marker ID, array of marker IDs, or `'*'` for all markers.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `openInfoWindow(markerId)`

Open the info window of a specific marker.

```js
map.openInfoWindow('33-address');
```

#### Arguments

- `markerId` (_string_|_array_|`'*'`) - A marker ID, array of marker IDs, or `'*'` for all info windows.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `closeInfoWindow(markerId)`

Close the info window of a specific marker.

```js
map.closeInfoWindow('33-address');
```

#### Arguments

- `markerId` (_string_|_array_|`'*'`) - A marker ID, array of marker IDs, or `'*'` for all info windows.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `hideCircle(circleId)`

Hide a circle. The circle will not be destroyed, it will simply be detached from the map.

```js
map.hideCircle('my-circle');
```

#### Arguments

- `circleId` (_string_|_array_|`'*'`) - A circle ID, array of circle IDs, or `'*'` for all circles.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `showCircle(circleId)`

Show a circle. The circle will be re-attached to the map.

```js
map.showCircle('my-circle');
```

#### Arguments

- `circleId` (_string_|_array_|`'*'`) - A circle ID, array of circle IDs, or `'*'` for all circles.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `hideKml(kmlId)`

Hide a KML layer. The KML layer will not be destroyed, it will simply be detached from the map.

```js
map.hideKml('my-kml');
```

#### Arguments

- `kmlId` (_string_|_array_|`'*'`) - A KML layer ID, array of KML layer IDs, or `'*'` for all KML layers.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `showKml(kmlId)`

Show a KML layer. The KML layer will be re-attached to the map.

```js
map.showKml('my-kml');
```

#### Arguments

- `kmlId` (_string_|_array_|`'*'`) - A KML layer ID, array of KML layer IDs, or `'*'` for all KML layers.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `hideGeoJson(geoJsonId)`

Hide a GeoJSON layer. The GeoJSON layer will not be destroyed, it will simply be detached from the map.

```js
map.hideGeoJson('my-geojson');
```

#### Arguments

- `geoJsonId` (_string_|_array_|`'*'`) - A GeoJSON layer ID, array of GeoJSON layer IDs, or `'*'` for all GeoJSON layers.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `showGeoJson(geoJsonId)`

Show a GeoJSON layer. The GeoJSON layer will be re-attached to the map.

```js
map.showGeoJson('my-geojson');
```

#### Arguments

- `geoJsonId` (_string_|_array_|`'*'`) - A GeoJSON layer ID, array of GeoJSON layer IDs, or `'*'` for all GeoJSON layers.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `hideGpx(gpxId)`

Hide a GPX layer. The GPX layer will not be destroyed, it will simply be detached from the map.

```js
map.hideGpx('my-gpx');
```

#### Arguments

- `gpxId` (_string_|_array_|`'*'`) - A GPX layer ID, array of GPX layer IDs, or `'*'` for all GPX layers.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `showGpx(gpxId)`

Show a GPX layer. The GPX layer will be re-attached to the map.

```js
map.showGpx('my-gpx');
```

#### Arguments

- `gpxId` (_string_|_array_|`'*'`) - A GPX layer ID, array of GPX layer IDs, or `'*'` for all GPX layers.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `hideGroundOverlay(overlayId)`

Hide a ground overlay. The ground overlay will not be destroyed, it will simply be detached from the map.

```js
map.hideGroundOverlay('my-overlay');
```

#### Arguments

- `overlayId` (_string_|_array_|`'*'`) - A ground overlay ID, array of ground overlay IDs, or `'*'` for all ground overlays.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `showGroundOverlay(overlayId)`

Show a ground overlay. The ground overlay will be re-attached to the map.

```js
map.showGroundOverlay('my-overlay');
```

#### Arguments

- `overlayId` (_string_|_array_|`'*'`) - A ground overlay ID, array of ground overlay IDs, or `'*'` for all ground overlays.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `hideDataset(datasetId)`

Hide a dataset. The dataset will not be destroyed, it will simply be hidden from view.

```js
map.hideDataset('my-dataset');
```

#### Arguments

- `datasetId` (_string_|_array_|`'*'`) - A dataset ID, array of dataset IDs, or `'*'` for all datasets.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `showDataset(datasetId)`

Show a dataset. The dataset will be shown again.

```js
map.showDataset('my-dataset');
```

#### Arguments

- `datasetId` (_string_|_array_|`'*'`) - A dataset ID, array of dataset IDs, or `'*'` for all datasets.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `hideBoundaries(boundariesId)`

Hide boundaries. The boundaries will not be destroyed, they will simply be hidden from view.

```js
map.hideBoundaries('my-boundaries');
```

#### Arguments

- `boundariesId` (_string_|_array_|`'*'`) - A boundaries ID, array of boundaries IDs, or `'*'` for all boundaries.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

### `showBoundaries(boundariesId)`

Show boundaries. The boundaries will be shown again.

```js
map.showBoundaries('my-boundaries');
```

#### Arguments

- `boundariesId` (_string_|_array_|`'*'`) - A boundaries ID, array of boundaries IDs, or `'*'` for all boundaries.

#### Returns

- _self_ - A chainable self-reference to this `DynamicMap` object.

---
---

## Non-Chainable Methods

:::warning Breaking the Chain
The following methods are the only ones which do not return a chainable map object.
:::

### `getMarker(markerId)`

Get the [Google Maps Marker object](https://developers.google.com/maps/documentation/javascript/reference/marker) of a specified marker.

```js
const marker = map.getMarker('33-address');
```

#### Arguments

- `markerId` (_string_) - The ID of the marker that you want to access.

#### Returns

- A Google Maps [Marker](https://developers.google.com/maps/documentation/javascript/reference/marker) object.

---
---

### `getInfoWindow(markerId)`

Get the [Google Maps Info Window object](https://developers.google.com/maps/documentation/javascript/infowindows) of a specified info window.

```js
const infoWindow = map.getInfoWindow('33-address');
```

#### Arguments

- `markerId` (_string_) - The ID of the marker with the info window that you want to access.

#### Returns

- A Google Maps [Info Window](https://developers.google.com/maps/documentation/javascript/infowindows) object.

---
---

### `getCircle(circleId)`

Get the [Google Maps Circle object](https://developers.google.com/maps/documentation/javascript/shapes#circles) of a specified circle.

```js
const circle = map.getCircle('my-circle');
```

#### Arguments

- `circleId` (_string_) - The ID of the circle that you want to access.

#### Returns

- A Google Maps [Circle](https://developers.google.com/maps/documentation/javascript/shapes#circles) object.

---
---

### `getKml(kmlId)`

Get the [Google Maps Data layer object](https://developers.google.com/maps/documentation/javascript/reference/data#Data) of a specified KML layer.

```js
const kml = map.getKml('my-kml');
```

#### Arguments

- `kmlId` (_string_) - The ID of the KML layer that you want to access.

#### Returns

- A Google Maps [Data layer](https://developers.google.com/maps/documentation/javascript/reference/data#Data) object.

:::warning Returns a Data layer
This method returns a `google.maps.Data` object, not a `google.maps.KmlLayer`. If your own JavaScript calls any `KmlLayer` methods directly, it will need to be updated to work with a `Data` object instead.
:::

---
---

### `getGeoJson(geoJsonId)`

Get the [Google Maps Data layer object](https://developers.google.com/maps/documentation/javascript/reference/data#Data) of a specified GeoJSON layer.

```js
const geoJson = map.getGeoJson('my-geojson');
```

#### Arguments

- `geoJsonId` (_string_) - The ID of the GeoJSON layer that you want to access.

#### Returns

- A Google Maps [Data layer](https://developers.google.com/maps/documentation/javascript/reference/data#Data) object.

---
---

### `getGpx(gpxId)`

Get the [Google Maps Data layer object](https://developers.google.com/maps/documentation/javascript/reference/data#Data) of a specified GPX layer.

```js
const gpx = map.getGpx('my-gpx');
```

#### Arguments

- `gpxId` (_string_) - The ID of the GPX layer that you want to access.

#### Returns

- A Google Maps [Data layer](https://developers.google.com/maps/documentation/javascript/reference/data#Data) object.

---
---

### `getGroundOverlay(overlayId)`

Get the [Google Maps Ground Overlay object](https://developers.google.com/maps/documentation/javascript/reference/image-overlay#GroundOverlay) of a specified ground overlay.

```js
const overlay = map.getGroundOverlay('my-overlay');
```

#### Arguments

- `overlayId` (_string_) - The ID of the ground overlay that you want to access.

#### Returns

- A Google Maps [Ground Overlay](https://developers.google.com/maps/documentation/javascript/reference/image-overlay#GroundOverlay) object.

---
---

### `getDataset(datasetId)`

Get the [Google Maps Feature Layer object](https://developers.google.com/maps/documentation/javascript/reference/data-driven-styling#FeatureLayer) of a specified dataset.

```js
const dataset = map.getDataset('my-dataset');
```

#### Arguments

- `datasetId` (_string_) - The ID of the dataset that you want to access.

#### Returns

- A Google Maps [Feature Layer](https://developers.google.com/maps/documentation/javascript/reference/data-driven-styling#FeatureLayer) object.

---
---

### `getBoundaries(boundariesId)`

Get the [Google Maps Feature Layer object](https://developers.google.com/maps/documentation/javascript/reference/data-driven-styling#FeatureLayer) of a specified set of boundaries.

```js
const boundaries = map.getBoundaries('my-boundaries');
```

#### Arguments

- `boundariesId` (_string_) - The ID of the boundaries that you want to access.

#### Returns

- A Google Maps [Feature Layer](https://developers.google.com/maps/documentation/javascript/reference/data-driven-styling#FeatureLayer) object.

---
---

### `getMarkerClusterer()`

If [clustering](/dynamic-maps/clustering-markers/) is enabled, get the [Google Maps MarkerClusterer object](https://googlemaps.github.io/js-markerclusterer/classes/MarkerClusterer.html).

```js
const clusterer = map.getMarkerClusterer();
```

#### Returns

- A Google Maps [MarkerClusterer](https://googlemaps.github.io/js-markerclusterer/classes/MarkerClusterer.html) object.

---
---

### `getZoom()`

Get the current zoom level.

```js
const level = map.getZoom();
```

#### Returns

- An integer between `1` - `22` representing the current zoom level.

---
---

### `getCenter()`

Get the center point coordinates of the map based on its current position.

```js
const coords = map.getCenter();
```

#### Returns

- A set of [coordinates](/models/coordinates/) representing the current map center.

---
---

### `getBounds()`

Get the [bounds](https://developers.google.com/maps/documentation/javascript/reference/coordinates#LatLngBounds) of the map based on its current position.

```js
const bounds = map.getBounds();
```

#### Returns

- A set of [bounds](https://developers.google.com/maps/documentation/javascript/reference/coordinates#LatLngBounds), which is effectively a pair of [coordinates](/models/coordinates/), representing the Southwest & Northeast corners of a rectangle.

---
---

### `tag(options = {})`

Creates a new `<div>` element, detached from the DOM. If a `parentId` is specified, the element will automatically be injected into the specified parent container.

```js
map.tag({'parentId': 'target-parent-id'});
```

#### Arguments

- `options` (_array_) - Configuration options for the rendered dynamic map.

| Option     | Type     | Default | Description
|:-----------|:--------:|:-------:|-------------
| `parentId` | _string_ | `null`  | The ID of the target parent container for the newly created element.

#### Returns

- A new DOM element. Will always return the newly created element, regardless of whether it was automatically injected into a parent container.
