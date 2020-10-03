# `dynamicmap.js`

This file contains the `DynamicMap` model, which can be used to create chainable JS map objects.

:::warning Don't access directly
It is extremely rare to need to access this model directly, you will almost always use the [`googleMaps` object](/javascript/googlemaps.js/) to create and retrieve map objects.
:::

For a more comprehensive explanation of how to use the internal API, check out the docs regarding the [Universal Methods](/maps/universal-methods/) and [JavaScript Methods](/maps/javascript-methods/).

## Public Methods

### `markers(locations, options = [])`

Append markers to an existing map object.

#### Arguments

 - `locations` (_array_|_coords_) - See a description of acceptable [locations...](/maps/locations/)
 - `options` (_array_) - Optional parameters to configure the markers. (see below)
 
| Option               | Type                 | Default | Description |
|----------------------|:--------------------:|:-------:|-------------|
| `id`                 | _string_             | <span style="white-space:nowrap">`"marker-{random}"`</span> | Reference point for each marker. |
| `icon`               | _object_ or _string_ | _null_  | An `icon` as defined by [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions.icon). |
| `markerOptions`      | _object_             | _null_  | Accepts any [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions) properties. |
| `infoWindowOptions`  | _object_             | _null_  | Accepts any [`google.maps.InfoWindowOptions`](https://developers.google.com/maps/documentation/javascript/reference/info-window#InfoWindowOptions) properties. |
| `infoWindowTemplate` | _string_             | _null_  | Template path to use for creating [info windows](/maps/info-windows/). |

#### Returns

_self_ - This instance of the `googleMaps` object. By returning a static self reference, chaining is possible.

```js
map.markers(locations);
```

---
---

### `kml(files, options = [])`

Append one or more KML layers to an existing map object.

#### Arguments

 - `files` (_array_|_string_)
 - `options` (_array_) - Optional parameters to configure the KML layers. (see below)
 
| Option             | Type     | Default | Description |
|--------------------|:--------:|:-------:|-------------|
| `KmlLayerOptions`  | _object_ | _null_  | Accepts any [`google.maps.KmlLayerOptions`](https://developers.google.com/maps/documentation/javascript/reference/kml#KmlLayerOptions) properties. |

#### Returns

_self_ - This instance of the `googleMaps` object. By returning a static self reference, chaining is possible.

```js
map.kml(files);
```

---
---

### `styles(stylesArray)`

Style a map based on a given array of styles.

:::tip Generating Styles
There are many ways to generate an array of map styles. The most popular approach is to use a service like one of the following:

 - example 1
 - example 2
:::

#### Arguments

 - `stylesArray` (_array_) - A set of styles to be applied to the map.

#### Returns

_self_ - This instance of the `googleMaps` object. By returning a static self reference, chaining is possible.

```js
map.styles(stylesArray);
```

---
---


### `zoom(level)`

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

---
---

### `center(coords)`

```js
map.center({
   "lat": 32.3113966,
   "lng": -64.7527469
});
```

Re-center the map.

#### `coords`

 - A simple key-value set of [coordinates](/models/coordinates/).

---
---

### `fit()`

```js
map.fit();
```

Zoom map to automatically fit all markers within the viewing area. Internally uses [`fitBounds`](https://developers.google.com/maps/documentation/javascript/reference/map#Map.fitBounds).

---
---

### `refresh()`

```js
map.refresh();
```

Refresh an existing map. You may need to do this after the page has been resized, or if something has been moved or changed.

---
---

### `getMarker(markerId)`

```js
var marker = map.getMarker(markerId);
```

#### `markerId`

 - The ID of the marker that you want to access.

#### Returns

 - A Google Maps [Marker](https://developers.google.com/maps/documentation/javascript/reference/marker) object.

---
---

### `panToMarker(markerId)`

```js
map.panToMarker('33.address');
```

Re-center map on the specified marker.

#### `markerId`

 - The ID of the marker that you want to pan to.

---
---

### `setMarkerIcon(markerId, icon)`

```js
map.setMarkerIcon('33.address', icon);
```

Set the icon of an existing marker. Internally uses [`setIcon`](https://developers.google.com/maps/documentation/javascript/reference/marker#Marker.setIcon).

#### `markerId`

 - The ID of the marker that you want to set the icon for.

#### `icon`

 - The icon to set on the specified marker. Can be either a _string_ or an [Icon Interface](https://developers.google.com/maps/documentation/javascript/reference/marker#Icon).

---
---

### `hideMarker(markerId)`

```js
map.hideMarker('33.address');
```

Hide a marker. The marker will not be destroyed, it will simply be detached from the map.

#### `markerId`

 - The ID of the marker that you want to hide.

---
---

### `showMarker(markerId)`

```js
map.showMarker('33.address');
```

Show a marker. The marker will be re-attached to the map.

#### `markerId`

 - The ID of the marker that you want to show.


---
---

### `tag()`

Creates a new `<div>` element, waiting to be placed in the DOM.

#### Returns

_node_ - A DOM element.

```js
// Get your map container
var container = document.getElementById('your-map-container');

// A div element containing the fully-rendered map
var mapDiv = map.tag();

// Add the map to your container
container.appendChild(mapDiv);
```
