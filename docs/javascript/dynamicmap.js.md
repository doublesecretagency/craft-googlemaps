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

- `url` (_string_) - Publicly accessible URL of the KML file.
- `options` (_array_) - Optional parameters to configure the KML layer. (see below)
 
| Option | Type     | Default | Description
|--------|:--------:|:-------:|-------------
| `id`   | _string_ | <span style="white-space:nowrap">`"kml-{random}"`</span> | Reference point for each KML layer.
| `kmlLayerOptions` | _object_ | _null_  | Accepts any [`google.maps.KmlLayerOptions`](https://developers.google.com/maps/documentation/javascript/reference/kml#KmlLayerOptions) properties.

```js
map.kml('https://googlearchive.github.io/js-v2-samples/ggeoxml/cta.kml');
```

:::tip Creating a KML file
KML files can be created using [Google My Maps](https://www.google.com/maps/about/mymaps/) or a similar service.
:::

:::warning Must be publicly accessible
In order for a KML file to work, it must exist at a publicly available URL.
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

Zoom and center map to fit all markers within the viewing area. Internally uses [`fitBounds`](https://developers.google.com/maps/documentation/javascript/reference/map#Map.fitBounds).

```js
map.fit();
```

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

Get the [Google Maps KML Layer object](https://developers.google.com/maps/documentation/javascript/kml) of a specified KML layer.

```js
const kml = map.getKml('my-kml');
```

#### Arguments

- `kmlId` (_string_) - The ID of the KML layer that you want to access.

#### Returns

- A Google Maps [KML Layer](https://developers.google.com/maps/documentation/javascript/kml) object.

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
