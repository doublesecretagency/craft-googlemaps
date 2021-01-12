# `googlemaps.js`

This file contains the `googleMaps` JavaScript object. Additionally, a `googleMaps` variable will be automatically set as a singleton in the global scope at runtime.

**Use this object as a starting point for creating maps.**

```js
var map = googleMaps.map(locations);
```

:::warning How it works
Internally, the `googleMaps` object can create multiple instances of the [`DynamicMap` model](/javascript/dynamicmap.js/). Each `DynamicMap` object will be directly tied to a Google Maps map on the page.

The `googleMaps` object also keeps a reference to all maps which have already been created, so you can easily access them later.
:::

For a more comprehensive explanation of how to use the internal API, check out the docs regarding the [Universal Methods](/dynamic-maps/universal-methods/) and [JavaScript Methods](/dynamic-maps/javascript-methods/).

## Map Management Methods

### `map(locations = [], options = {})`

Calling this method will create a new [`DynamicMap` map object](/javascript/dynamicmap.js/).

```js
// Marker locations
var locations = [
    {'lat': 40.730610, 'lng': -73.935242},  // New York
    {'lat': 34.052235, 'lng': -118.243683}, // Los Angeles
    {'lat': 41.881832, 'lng': -87.623177}   // Chicago
];

// Map options
var options = {
    'id': 'us-cities',
    'height': 300,
    'zoom': 5
};

// Create a new DynamicMap object
var map = googleMaps.map(locations, options);
```

The map object is a starting point which sets the map-building chain in motion. You will be able to build upon the map by adding markers, KML layers, etc.

Once you have the map object in hand, you can then chain methods from within the `DynamicMap` object to further customize the map. There is no limit as to how many methods you can chain, nor what order they should appear in.

#### Arguments

 - `locations` (_[coords](/models/coordinates/)_|_array_) - A single set of coordinates, or an array of coordinate sets.
 - `options` (_array_) - Optional parameters to configure the map. (see below)

| Option               | Type                | Default            | Description |
|----------------------|:-------------------:|:------------------:|-------------|
| `id`                 | _string_            | <span style="white-space:nowrap">`"map-{random}"`</span> | Set the `id` attribute of the map container. |
| `width`              | _int_               | _null_             | Set the width of the map (in px). |
| `height`             | _int_               | _null_             | Set the height of the map (in px). |
| `zoom`               | _int_               | (uses `fitBounds`) | Set the default zoom level of the map. <span style="white-space:nowrap">(`1`-`22`)</span> |
| `center`             | _[coords](/models/coordinates/)_ | (uses `fitBounds`) | Set the center position of the map. |
| `styles`             | _array_             | _null_             | An array of [map styles](/guides/styling-a-map/). |
| `mapOptions`         | _object_            | _null_             | Accepts any [`google.maps.MapOptions`](https://developers.google.com/maps/documentation/javascript/reference/map#MapOptions) properties. |
| `markerOptions`      | _object_            | _null_             | Accepts any [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions) properties. |
| `infoWindowOptions`  | _object_            | _null_             | Accepts any [`google.maps.InfoWindowOptions`](https://developers.google.com/maps/documentation/javascript/reference/info-window#InfoWindowOptions) properties. |
| `infoWindowTemplate` | _string_            | _null_             | Template path to use for creating [info windows](/dynamic-maps/info-windows/). |

#### Returns

 - A chainable `DynamicMap` object.

:::tip Locations are Skippable
If you omit the `locations` parameter, or pass in an empty array, a blank map will be created.
:::

---
---

### `getMap(mapId)`

```js
var map = googleMaps.getMap('my-map');
```

Retrieve an existing map object.

#### Arguments

 - `mapId` (_string_) - The ID of the map that you want to access.

#### Returns

 - A chainable `DynamicMap` object.

---
---

## Map Initialization Methods

### `init(mapId = null, callback = null)`

```js
googleMaps.init();
```

Initialize a map, or a group of maps. This will be automatically run (unless disabled) for each map on the page.

#### Arguments

 - `mapId` (_string_|_array_|_null_) - The ID of the map that you want to access. You can also specify an array of map IDs. You can also pass _null_ (or omit both parameters) to initialize all maps on the page.
 - `callback` (_function_) - An optional callback function, to be executed after the map has finished loading.

Depending on what is specified as the `mapId` value, the `init` method can initialize one or many maps simultaneously.

```js
// Null - Initialize all maps on the page
googleMaps.init();

// String - Initialize only the specified map
googleMaps.init('my-map');

// Array - Initialize all specified maps
googleMaps.init(['map-one', 'map-two', 'map-three']);
```

You can specify a `callback` function to be run after the map has loaded.

```js
// Pass callback function by reference
googleMaps.init('my-map', myCallbackFunction);

// Pass anonymous callback function
googleMaps.init('my-map', function () {
    console.log("The map has finished loading!");
});
```

---
---

## Public Properties

### `log`

#### Type

 - _bool_ - Determines whether the JavaScript methods should log their progress to the console.

Set to `false` by default. Can be enabled by setting to `true` before any methods have been run.

:::tip devMode
The `log` property will automatically be set to `true` when [`devMode`](https://craftcms.com/knowledge-base/what-dev-mode-does) is enabled.
:::
