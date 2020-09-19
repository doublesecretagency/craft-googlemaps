# JavaScript Methods

In addition to all the [Universal Methods](/maps/universal-methods/) available in the API, there are a few more methods that are available exclusively in JavaScript.

These methods have no PHP or Twig equivalent because they make no sense outside of the JavaScript context.

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
