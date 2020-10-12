# Additional JavaScript Methods

In addition to all the [Universal Methods](/dynamic-maps/universal-methods/) available in the API, there are a few more methods that are available exclusively in JavaScript.

## `tag(parentId = null)`

**Ends the map chain.** Creates a new `<div>` element, to be placed in the DOM as you wish.

:::code
```js
// Inject a map into the DOM
map.tag('parent-id');
```
:::

:::warning Same But Different
The `tag` method also exists in [Twig & PHP](/dynamic-maps/twig-php-methods/#tag-init-true), but beware that the usage is notably different.
:::

#### `parentId`

 - Optional. If provided, automatically injects the new HTML element into the DOM container with the specified `id` attribute.
 
:::tip Automatic vs Manual Placement
If the `parentId` is specified, the new HTML element will be automatically appended to the specified parent container in the DOM.

If the `parentId` is omitted, the new HTML element will need to be manually placed into the DOM at your discretion.
:::

:::code
```js Automatic Placement
// Place the HTML element automatically
map.tag('parent-id');
````
```js Manual Placement
// Place the HTML element manually
var mapDiv = map.tag();
document.getElementById('parent-id').appendChild(mapDiv);
```
:::

#### Returns

 - A new `<div>` element which holds the fully-rendered map. If no `parentId` was specified, the element will still need to be manually added to the DOM.
 
:::warning Always returns an HTML element
The new HTML element will always be returned by the `tag` method, _regardless_ of whether it was automatically injected into the DOM. 
:::

## `init(mapId = null)`

Initialize a map, or a group of maps. This will be automatically run (unless disabled) for each map on the page.

#### `mapId`

 - Depending on what is specified as the `mapId` value, the `init` method can initialize one or many maps simultaneously.

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

---
---

## `getMarker(markerId)`

:::code
```js
var marker = map.getMarker(markerId);
```
:::

#### `markerId`

 - The ID of the marker that you want to access.

#### Returns

 - A Google Maps [Marker](https://developers.google.com/maps/documentation/javascript/reference/marker) object.
