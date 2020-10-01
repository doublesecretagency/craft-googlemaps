# JavaScript Methods

In addition to all the [Universal Methods](/maps/universal-methods/) available in the API, there are several more methods that are available exclusively in JavaScript. These methods have no PHP or Twig equivalent, because they make no sense outside of the JavaScript context.

**Remember, almost all of these methods can be chained together!** That includes using the methods outlined on the [Universal Methods](/maps/universal-methods/) page.

```js
// Create a new map, then style the map
googleMaps.map(locations, {'id':'my-map'}).styles(stylesArray);

// Get the existing map, pan to marker, then adjust zoom level
googleMaps.getMap('my-map').panToMarker('33.address').zoom(10)
```

## `zoom(level)`

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

## `center(coords)`

```js
map.center({
   "lat": 32.3113966,
   "lng": -64.7527469
});
```

Re-center the map.

#### `coords`

 - A simple key-value set of [coordinates](/models/coordinates/).

## `fit()`

```js
map.fit();
```

Zoom map to automatically fit all markers within the viewing area. Internally uses [`fitBounds`](https://developers.google.com/maps/documentation/javascript/reference/map#Map.fitBounds).

## `refresh()`

```js
map.refresh();
```

Refresh an existing map. You may need to do this after the page has been resized, or if something has been moved or changed.

## `panToMarker(markerId)`

```js
map.panToMarker('33.address');
```

Re-center map on the specified marker.

#### `markerId`

 - The ID of the marker that you want to pan to.

## `setMarkerIcon(markerId, icon)`

```js
map.setMarkerIcon('33.address', icon);
```

Set the icon of an existing marker. Internally uses [`setIcon`](https://developers.google.com/maps/documentation/javascript/reference/marker#Marker.setIcon).

#### `markerId`

 - The ID of the marker that you want to set the icon for.

#### `icon`

 - The icon to set on the specified marker. Can be either a _string_ or an [Icon Interface](https://developers.google.com/maps/documentation/javascript/reference/marker#Icon).

## `hideMarker(markerId)`

```js
map.hideMarker('33.address');
```

Hide a marker. The marker will not be destroyed, it will simply be detached from the map.

#### `markerId`

 - The ID of the marker that you want to hide.

## `showMarker(markerId)`

```js
map.showMarker('33.address');
```

Show a marker. The marker will be re-attached to the map.

#### `markerId`

 - The ID of the marker that you want to show.

## `tag(parentId = null)`

```js
// Place the map element automatically
map.tag('my-target-div');

// Place the map element manually
var mapDiv = map.tag();
document.getElementById('my-target-div').appendChild(mapDiv);
```

**Ends the map chain.** Creates a new `<div>` element, to be placed in the DOM as you wish.

#### `parentId`

 - Automatically inject the map element into the DOM container with the specified `id` attribute.

:::tip Automatic Placement
If `parentId` is specified, the new map will be automatically appended to that container.
:::

#### Returns

 - A new `<div>` element which holds the fully-rendered map. If no `parentId` was specified, the element will need to be manually added to the DOM.

## `init(mapId = null)`

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
