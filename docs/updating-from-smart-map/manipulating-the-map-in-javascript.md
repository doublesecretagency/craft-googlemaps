---
description:
---

# 🔧 Manipulating the map in JavaScript

<update-message/>

In Smart Map, you were relegated to JavaScript in order to accomplish things which could not otherwise be done in Twig. In the new Google Maps plugin, JavaScript is treated as an [equal partner](/dynamic-maps/universal-api/) alongside Twig (and PHP).

:::code
```js
const map = googleMaps.getMap(mapId);
```
```twig
{% set map = googleMaps.getMap(mapId) %}
```
```php
$map = GoogleMaps::getMap($mapId);
```
:::

## Maps

When creating a map, it's possible to manually specify the ID of each map. But if you _don't_ specify an ID, one will be assigned to the map automatically.

For automatically generated IDs, the **naming pattern** has changed between versions:

```js
// OLD: Automatically incremented from 1
'smartmap-mapcanvas-{int}'

// NEW: Randomly generated string of six lowercase letters
'map-{random}'
```

We've also changed how you access an existing map:

```js
// OLD
const map = smartMap.map['my-map'];

// NEW
const map = googleMaps.getMap('my-map');
```

Overall, the new `map` object is _much_ more powerful and flexible. Once you have a [map object](/javascript/googlemaps.js/#map-locations-options) in hand, it's easy to [chain](/dynamic-maps/chaining/) other configuration commands.

## Markers

You can add location markers when initially [creating the map](/dynamic-maps/basic-map-management/#map-locations-options), or later using the [`markers` method](/dynamic-maps/universal-methods/#markers-locations-options).

:::code
```js
map.markers(locations, options);
```
```twig
{% do map.markers(locations, options) %}
```
```php
$map->markers($locations, $options);
```
:::

In JavaScript, you can also access the raw [Google Marker](https://developers.google.com/maps/documentation/javascript/reference/marker) object:

```js
const marker = map.getMarker(markerId);
```

## Info Windows

You can easily create info windows using [Twig or PHP](/dynamic-maps/info-windows/).

In JavaScript, you can also access the raw [Google Info Window](https://developers.google.com/maps/documentation/javascript/reference/info-window) object:

```js
const infoWindow = map.getInfoWindow(markerId);
```

For more information, see the guide on [Opening Info Windows...](/guides/opening-info-windows/)

## Additional Functions

For a complete list of what is now possible in JavaScript, read up on the following concepts:

 - [Map Management](/dynamic-maps/basic-map-management/)
 - [Universal Methods](/dynamic-maps/universal-methods/)
 - [Additional JavaScript Methods](/dynamic-maps/javascript-methods/)

:::tip New Documentation
See the complete new [Dynamic Maps](/dynamic-maps/) documentation.
:::
