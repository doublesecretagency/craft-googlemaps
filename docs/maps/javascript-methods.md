# Additional JavaScript Methods

In addition to all the [Universal Methods](/maps/universal-methods/) available in the API, there are several more methods that are available exclusively in JavaScript.

## `tag(parentId = null)`

**Ends the map chain.** Creates a new `<div>` element, to be placed in the DOM as you wish.

```js
// Place the map element automatically
map.tag('my-target-div');

// Place the map element manually
var mapDiv = map.tag();
document.getElementById('my-target-div').appendChild(mapDiv);
```

:::warning Same But Different
The `tag` method also exists in [Twig & PHP](/maps/twig-php-methods/#tag-init-true), although the usage is very different.
:::

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

## `getMarker(markerId)`

```js
var marker = map.getMarker(markerId);
```

#### `markerId`

 - The ID of the marker that you want to access.

#### Returns

 - A Google Maps [Marker](https://developers.google.com/maps/documentation/javascript/reference/marker) object.
