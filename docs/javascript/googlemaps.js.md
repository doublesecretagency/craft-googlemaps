# `googleMaps.js`

This file will automatically load the `googleMaps` JavaScript object into the global scope.

**Use this object as a starting point for creating maps.**

:::warning How it works
Internally, the `googleMaps` object will create instances of the [`DynamicMap` model](/javascript/dynamicmap.js/). Each new map will generate its own `DynamicMap` object, which can then be used to chain additional behaviors as desired.
:::

For a more comprehensive explanation of how to use the internal API, check out the docs regarding the [Universal Methods](/maps/universal-methods/) and [JavaScript Methods](/maps/javascript-methods/).

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

### `getMap(mapId)`

```js
var map = googleMaps.getMap(mapId);
```

Retrieve an existing map object.

#### `mapId`

 - The ID of the map that you want to access.

#### Returns

 - Map object (for chaining)

---
---

### `init(mapId = null)`

```js
googleMaps.init();
```

Initialize a map, or a group of maps. This will be automatically run (unless disabled) for each map on the page.

#### `mapId`

Depending on what is specified as the `mapId` value, the `init` method can initialize one or many maps simultaneously.

```js
// Null - Initialize all maps on the page
googleMaps.init();

// String - Initialize only the specified map
googleMaps.init('my-custom-map');

// Array - Initialize all specified maps
googleMaps.init(['map-one', 'map-two', 'map-three']);
```

---
---

## Public Properties

### `log`

#### Type

 - _bool_ - Determines whether the JavaScript methods should log their progress to the console.

Set to `false` by default. Will be set to `true` automatically when [`devMode`](https://craftcms.com/knowledge-base/what-dev-mode-does) is enabled. Can be enabled manually by simply setting it to `true` in JavaScript.

