---
description: A few additional methods are only available in JavaScript. Explore what else is possible when working with dynamic maps in JavaScript.
---

# Additional JavaScript Methods

In addition to all the [Universal Methods](/dynamic-maps/universal-methods/) available in the API, there are a few more methods that are available exclusively in JavaScript.

## `tag(options = {})`

**Ends the map chain.** Creates a new `<div>` element, to be placed in the DOM as you wish.

:::code
```js
// Inject a map into the DOM
map.tag({'parentId': 'target-parent-id'});
```
:::

:::warning Same But Different
The `tag` method also exists in [Twig & PHP](/dynamic-maps/twig-php-methods/#tag-options), but beware that the usage is notably different.
:::

#### Arguments

 - `options` (_array_) - Configuration options for the rendered dynamic map.

| Option     | Type     | Default | Description
|:-----------|:--------:|:-------:|-------------
| `parentId` | _string_ | `null`  | The ID of the target parent container for the newly created element.

If the `parentId` is provided, the new HTML element will be automatically injected into the DOM container with the specified `id` attribute.

:::code
```js Automatic Placement
// Place the HTML element automatically
map.tag({'parentId': 'target-parent-id'});
```
```js Manual Placement
// Place the HTML element manually
const mapDiv = map.tag();
document.getElementById('target-parent-id').appendChild(mapDiv);
```
:::
 
:::tip Automatic vs Manual Placement
If the `parentId` is specified, the new HTML element will be automatically appended to the specified parent container in the DOM.

If the `parentId` is omitted, the new HTML element must be manually placed into the DOM at your discretion.
:::

#### Returns

 - A new `<div>` element which holds the fully-rendered map. If no `parentId` was specified, the element will still need to be manually added to the DOM.
 
:::warning Always returns an HTML element
The new HTML element will always be returned by the `tag` method, _regardless_ of whether it was automatically injected into the DOM. 
:::

---
---

## `init(mapId = null, callback = null)`

Initialize a map, or a group of maps. This will be automatically run (unless disabled) for each map on the page.

#### Arguments

 - `mapId` (_null_|_string_|_array_) - Depending on what is specified as the `mapId` value, the `init` method can initialize one or many maps simultaneously.
 - `callback` (_function_) - The `callback` function will be triggered immediately after the map has finished loading.

:::code
```js Null
// Initialize all maps on the page
googleMaps.init();
```
```js String
// Initialize only the specified map
googleMaps.init('my-custom-map');
```
```js Array
// Initialize all specified maps
googleMaps.init(['map-one', 'map-two', 'map-three']);
```
:::

:::code
```js Callback by Reference
// Pass the callback function by reference
googleMaps.init('my-custom-map', myCallbackFunction);
```
```js Callback as Anonymous Function
// Pass an anonymous callback function
googleMaps.init('my-custom-map', function () {
    console.log("The map has finished loading!");
});
```
:::

#### Returns

 - A promise, resolved once the map has finished loading.

---
---

## `getMarker(markerId)`

Get the Google Maps [Marker](https://developers.google.com/maps/documentation/javascript/reference/marker) object of the specified marker.

:::code
```js
// Get the specified Marker object
const marker = map.getMarker(markerId);
```
:::

#### Arguments

 - `markerId` (_string_) - The ID of the marker that you want to access.

#### Returns

 - A Google Maps [Marker](https://developers.google.com/maps/documentation/javascript/reference/marker) object.

---
---

## `getInfoWindow(markerId)`

Get the Google Maps [Info Window](https://developers.google.com/maps/documentation/javascript/infowindows) object of the specified info window.

:::code
```js
// Get the specified Info Window object
const infoWindow = map.getInfoWindow(markerId);
```
:::

#### Arguments

 - `markerId` (_string_) - The ID of the marker with the info window that you want to access.

#### Returns

 - A Google Maps [Info Window](https://developers.google.com/maps/documentation/javascript/infowindows) object.

---
---

## `getCircle(circleId)`

Get the Google Maps [Circle](https://developers.google.com/maps/documentation/javascript/shapes#circles) object of the specified circle.

:::code
```js
// Get the specified circle object
const circle = map.getCircle(circleId);
```
:::

#### Arguments

- `circleId` (_string_) - The ID of the circle that you want to access.

#### Returns

- A Google Maps [Circle](https://developers.google.com/maps/documentation/javascript/shapes#circles) object.

---
---

## `getKml(kmlId)`

Get the Google Maps [Data layer](https://developers.google.com/maps/documentation/javascript/reference/data#Data) object of the specified KML layer.

:::code
```js
// Get the specified KML layer object
const kml = map.getKml(kmlId);
```
:::

#### Arguments

- `kmlId` (_string_) - The ID of the KML layer that you want to access.

#### Returns

- A Google Maps [Data layer](https://developers.google.com/maps/documentation/javascript/reference/data#Data) object.

:::warning Returns a Data layer
This method returns a `google.maps.Data` object, not a `google.maps.KmlLayer`. If your own JavaScript calls any `KmlLayer` methods directly, it will need to be updated to work with a `Data` object instead.
:::

---
---

## `getGeoJson(geoJsonId)`

Get the Google Maps [Data layer](https://developers.google.com/maps/documentation/javascript/reference/data#Data) object of the specified GeoJSON layer.

:::code
```js
// Get the specified GeoJSON layer object
const geoJson = map.getGeoJson(geoJsonId);
```
:::

#### Arguments

- `geoJsonId` (_string_) - The ID of the GeoJSON layer that you want to access.

#### Returns

- A Google Maps [Data layer](https://developers.google.com/maps/documentation/javascript/reference/data#Data) object.

---
---

## `getGpx(gpxId)`

Get the Google Maps [Data layer](https://developers.google.com/maps/documentation/javascript/reference/data#Data) object of the specified GPX layer.

:::code
```js
// Get the specified GPX layer object
const gpx = map.getGpx(gpxId);
```
:::

#### Arguments

- `gpxId` (_string_) - The ID of the GPX layer that you want to access.

#### Returns

- A Google Maps [Data layer](https://developers.google.com/maps/documentation/javascript/reference/data#Data) object.

---
---

## `getGroundOverlay(overlayId)`

Get the Google Maps [Ground Overlay](https://developers.google.com/maps/documentation/javascript/reference/image-overlay#GroundOverlay) object of the specified ground overlay.

:::code
```js
// Get the specified ground overlay object
const overlay = map.getGroundOverlay(overlayId);
```
:::

#### Arguments

- `overlayId` (_string_) - The ID of the ground overlay that you want to access.

#### Returns

- A Google Maps [Ground Overlay](https://developers.google.com/maps/documentation/javascript/reference/image-overlay#GroundOverlay) object.

---
---

## `getDataset(datasetId)`

Get the Google Maps [Feature Layer](https://developers.google.com/maps/documentation/javascript/reference/data-driven-styling#FeatureLayer) object of the specified dataset.

:::code
```js
// Get the specified dataset object
const dataset = map.getDataset(datasetId);
```
:::

#### Arguments

- `datasetId` (_string_) - The ID of the dataset that you want to access.

#### Returns

- A Google Maps [Feature Layer](https://developers.google.com/maps/documentation/javascript/reference/data-driven-styling#FeatureLayer) object.

---
---

## `getBoundaries(boundariesId)`

Get the Google Maps [Feature Layer](https://developers.google.com/maps/documentation/javascript/reference/data-driven-styling#FeatureLayer) object of the specified boundaries.

:::code
```js
// Get the specified boundaries object
const boundaries = map.getBoundaries(boundariesId);
```
:::

#### Arguments

- `boundariesId` (_string_) - The ID of the boundaries that you want to access.

#### Returns

- A Google Maps [Feature Layer](https://developers.google.com/maps/documentation/javascript/reference/data-driven-styling#FeatureLayer) object.

---
---

## `getMarkerClusterer()`

If clustering is [enabled](/dynamic-maps/clustering-markers/), get the map's [marker clustering](https://googlemaps.github.io/js-markerclusterer/) object.

:::code
```js
// Get the map's marker cluster object
const clusterer = map.getMarkerClusterer();
```
:::

#### Returns

- A [marker clustering](https://googlemaps.github.io/js-markerclusterer/) object. Will return `false` if clustering is not enabled.

---
---

## `getZoom()`

Get the current zoom level.

:::code
```js
// Get the current zoom level of the map
const level = map.getZoom();
```
:::

#### Returns

- An integer between `1` - `22` representing the current zoom level.

---
---

## `getCenter()`

Get the center point coordinates of the map based on its current position.

:::code
```js
// Get the current center point of the map
const coords = map.getCenter();
```
:::

#### Returns

- A set of [coordinates](/models/coordinates/) representing the current map center.

---
---

## `getBounds()`

Get the [bounds](https://developers.google.com/maps/documentation/javascript/reference/coordinates#LatLngBounds) of the map based on its current position.

:::code
```js
// Get the current bounds of the map
const bounds = map.getBounds();
```
:::

#### Returns

- A set of [bounds](https://developers.google.com/maps/documentation/javascript/reference/coordinates#LatLngBounds), which is effectively a pair of [coordinates](/models/coordinates/), representing the Southwest & Northeast corners of a rectangle.
