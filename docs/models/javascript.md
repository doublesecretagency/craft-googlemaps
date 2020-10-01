# JavaScript

There are two JavaScript files which get automatically loaded into the front-end (though this can be disabled) whenever you add a map to a page. They will be dynamically loaded into the `cpresources` folder.

```
/cpresources/.../js/dynamicmap.js"
/cpresources/.../js/googlemaps.js"
```

This will load an object called `googleMaps`, which contains the following methods...

## Public Methods

### `map(locations = [], options = [])`

Calling this method will create a new map object. It creates a starting point which sets the map-building chain in motion. You will be able to build upon the map by adding markers, KML layers, etc.

```js
var map = googleMaps.map(locations);
```

:::warning The "map" variable
For each of the remaining examples on this page, the `map` variable will be an instance of the `googleMaps` object. In each example, you will see how map methods can be chained at will.

It will be assumed that the `map` object has already been initialized, as demonstrated above.
:::

Once you have the map object in hand, you can then chain other methods to further customize the map. There is no limit as to how many methods you can chain, nor what order they should be in.

#### Arguments

 - `locations` (_array_|_coords_) - See a description of acceptable [locations...](/maps/locations/)
 - `options` (_array_) - Optional parameters to configure the map. (see below)

| Option               | Type                | Default            | Description |
|----------------------|:-------------------:|:------------------:|-------------|
| `id`                 | _string_            | <span style="white-space:nowrap">`"map-{random}"`</span> | Set the `id` attribute of the map container. |
| `width`              | _int_               | _null_             | Set the width of the map (in px). |
| `height`             | _int_               | _null_             | Set the height of the map (in px). |
| `zoom`               | _int_               | (uses `fitBounds`) | Set the default zoom level of the map. <span style="white-space:nowrap">(`1`-`22`)</span> |
| `center`             | [coords](/models/coordinates/) | (uses `fitBounds`) | Set the center position of the map. |
| `styles`             | _array_             | _null_             | An array of [map styles](/guides/styling-a-map/). |
| `mapOptions`         | _object_            | _null_             | Accepts any [`google.maps.MapOptions`](https://developers.google.com/maps/documentation/javascript/reference/map#MapOptions) properties. |
| `markerOptions`      | _object_            | _null_             | Accepts any [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions) properties. |
| `infoWindowOptions`  | _object_            | _null_             | Accepts any [`google.maps.InfoWindowOptions`](https://developers.google.com/maps/documentation/javascript/reference/info-window#InfoWindowOptions) properties. |
| `infoWindowTemplate` | _string_            | _null_             | Template path to use for creating [info windows](/maps/info-windows/). |

#### Returns

_self_ - This instance of the `googleMaps` object. By returning a static self reference, chaining is possible.

:::tip Locations are Skippable
If you skip the `locations` parameter, a blank map will be created.
:::

---
---

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

:::code
```js
map.markers(locations);
```
:::

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
