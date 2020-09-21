# JavaScript Methods

In addition to all the [Universal Methods](/maps/universal-methods/) available in the API, there are a few more methods that are available exclusively in JavaScript.

These methods have no PHP or Twig equivalent because they make no sense outside of the JavaScript context.

## `init(mapId = null)`

```js
googleMaps.init();
```

Initialize a map, or a group of maps. This will be automatically run (unless disabled) for each map on the page.

#### `mapId`

Depending on what is specified as the `mapId` value, the `init` method can initialize one or many maps simultaneously.

```js
// String - Initialize only the specified map
mapId = 'my-custom-map';

// Array - Initialize all specified maps
mapId = ['map-one', 'map-two', 'map-three'];

// Null - Initialize all maps on the page
mapId = null;
```

## `refresh()`

```js
map.refresh();
```

Refresh an existing map. You may need to do this after the page has been resized, or if something has been moved or changed.

## `hideMarker(markerId)`

```js
map.hideMarker(markerId);
```

Hide a marker. The marker will not be destroyed, it will simply be removed from the map.

## `showMarker(markerId)`

```js
map.showMarker(markerId);
```

Show a marker. The marker will be placed back onto the map.

## `setMarkerIcon(markerId, options)`

```js
map.setMarkerIcon(markerId, options);
```

Set the icon of an existing marker. The `options` object should take the form of a Google [Icon Interface](https://developers.google.com/maps/documentation/javascript/reference/marker#Icon). You can pass a collection of `key:value` pairs to specify the icon details.

## `panToMarker(markerId)`

```js
map.panToMarker(markerId);
```

Re-center map on the specified marker.

## `zoom(level)`

```js
map.zoom(level);
```

Change the map's zoom level. The `level` value must be an integer between 1 - 22.

## `tag()`

```js
var mapDiv = map.tag();
```

Creates a new `<div>` element, to be placed in the DOM as you wish.

#### Returns

 - A new `<div>` element which holds the fully-rendered map.
 
:::warning Must be placed manually
The new element will be created as an orphan. It will not exist in the DOM until you place it somewhere.
::: 
