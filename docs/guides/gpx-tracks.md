---
description: Show a hike, a bike ride, or any other recorded route on a map. Most GPS devices and fitness apps can export a GPX file.
---

# GPX Tracks

If you've recorded a route with a GPS device or a fitness app, chances are it can be exported as a [GPX file](https://en.wikipedia.org/wiki/GPS_Exchange_Format). Hikes, bike rides, runs, and delivery routes can all be drawn onto a dynamic map.

The `gpx` method is part of the [Universal API](/dynamic-maps/universal-methods/#gpx-url-options), so you can use it in any available language...

:::code
```js
map.gpx(url, options);
```
```twig
{% do map.gpx(url, options) %}
```
```php
$map->gpx($url, $options);
```
:::

Practically speaking, your code could look something like this...

:::code
```js
// Create a plain dynamic map
const map = googleMaps.map();

// Add a GPX track to the map
map.gpx('/files/morning-hike.gpx', {'id': 'my-gpx'});
```
```twig
{# Create a plain dynamic map #}
{% set map = googleMaps.map() %}

{# Add a GPX track to the map #}
{% do map.gpx('/files/morning-hike.gpx', {'id': 'my-gpx'}) %}
```
```php
// Create a plain dynamic map
$map = GoogleMaps::map();

// Add a GPX track to the map
$map->gpx('/files/morning-hike.gpx', ['id' => 'my-gpx']);
```
:::

Tracks, routes, and waypoints will all appear on the map. Once the file has loaded, the map will zoom to show the entire layer (unless you set `preserveViewport`).

## The `url` parameter

The file is fetched by the visitor's browser, so relative and local URLs work just fine. However, a file hosted on another domain may not load. See [Files on another domain...](/guides/kml-layers/#files-on-another-domain)

## The `options` parameter

All [options](/dynamic-maps/universal-methods/#gpx-url-options) are optional.

| Option                | Type               | Description |
|-----------------------|--------------------|-------------|
| `id`                  | _string_           | Reference point for each GPX layer. |
| `preserveViewport`    | _bool_             | If `true`, the map will not zoom to fit the layer once it loads. |
| `suppressInfoWindows` | _bool_             | If `true`, clicking a track or waypoint will not open an info window. |
| `clickable`           | _bool_             | If `false`, the layer will ignore clicks entirely (and no info windows will open). |
| `zIndex`              | _int_              | Stacking order of the layer, relative to other layers. |
| `style`               | _object_           | Any [`google.maps.Data.StyleOptions`](https://developers.google.com/maps/documentation/javascript/reference/data#Data.StyleOptions) properties. |

## Styling the track

Most GPX files carry no styles of their own, so a track without one will be drawn as a **blue line** by default. To change how the line looks, use the `style` option...

:::code
```js
map.gpx(url, {
    'style': {
        strokeColor: '#D9480F',
        strokeOpacity: 0.8,
        strokeWeight: 5
    }
});
```
```twig
{% do map.gpx(url, {
    'style': {
        'strokeColor': '#D9480F',
        'strokeOpacity': 0.8,
        'strokeWeight': 5
    }
}) %}
```
```php
$map->gpx($url, [
    'style' => [
        'strokeColor' => '#D9480F',
        'strokeOpacity' => 0.8,
        'strokeWeight' => 5
    ]
]);
```
:::

In JavaScript, the `style` option can also be a [styling function](https://developers.google.com/maps/documentation/javascript/reference/data#Data.StylingFunction), which receives each track or waypoint and returns its style.

Clicking a track or waypoint will open an info window showing its name and description (if it has them). To turn the info windows off, set `suppressInfoWindows` to `true`.

## Hiding or showing a GPX layer

To dynamically hide or show a GPX layer, use [`hideGpx`](/dynamic-maps/universal-methods/#hidegpx-gpxid) and [`showGpx`](/dynamic-maps/universal-methods/#showgpx-gpxid)...

:::code
```js
// Hide the GPX layer
map.hideGpx('my-gpx');

// Show the GPX layer
map.showGpx('my-gpx');
```
```twig
{# Hide the GPX layer #}
{% do map.hideGpx('my-gpx') %}

{# Show the GPX layer #}
{% do map.showGpx('my-gpx') %}
```
```php
// Hide the GPX layer
$map->hideGpx('my-gpx');

// Show the GPX layer
$map->showGpx('my-gpx');
```
:::

### Beyond hiding and showing

When you need to do more than hide or show a layer, you can get the raw [Google Data layer object](https://developers.google.com/maps/documentation/javascript/reference/data#Data), and manipulate it further using Google's own API.

This method is only available [in JavaScript](/dynamic-maps/javascript-methods/#getgpx-gpxid).

```js
// Get the raw Google Data layer object
const gpx = map.getGpx('my-gpx');
```
