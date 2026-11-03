---
description: Place an image directly onto a map. A floor plan, a campus map, or a historic map can be stretched to cover the exact area it shows.
---

# Ground Overlays

Sometimes the most useful map is an image. A floor plan, a campus map, or a historic map can be placed directly onto a dynamic map, and stretched to cover the exact area it shows. Google calls this a [ground overlay](https://developers.google.com/maps/documentation/javascript/groundoverlays).

The `groundOverlay` method is part of the [Universal API](/dynamic-maps/universal-methods/#groundoverlay-url-bounds-options), so you can use it in any available language...

:::code
```js
map.groundOverlay(url, bounds, options);
```
```twig
{% do map.groundOverlay(url, bounds, options) %}
```
```php
$map->groundOverlay($url, $bounds, $options);
```
:::

The `url` is the location of the image, and the `bounds` determine where the image will be placed. Each edge of the image is pinned to a line of latitude or longitude...

:::code
```js
map.groundOverlay('/images/historic-bermuda.jpg', {
    'north': 32.3960,
    'south': 32.2460,
    'east': -64.6420,
    'west': -64.8880
}, {
    'id': 'my-overlay',
    'opacity': 0.75
});
```
```twig
{% do map.groundOverlay('/images/historic-bermuda.jpg', {
    'north': 32.3960,
    'south': 32.2460,
    'east': -64.6420,
    'west': -64.8880
}, {
    'id': 'my-overlay',
    'opacity': 0.75
}) %}
```
```php
$map->groundOverlay('/images/historic-bermuda.jpg', [
    'north' => 32.3960,
    'south' => 32.2460,
    'east' => -64.6420,
    'west' => -64.8880
], [
    'id' => 'my-overlay',
    'opacity' => 0.75
]);
```
:::

The image will be stretched to fill those bounds, so be sure the image has been cropped to match the area it covers.

## The `options` parameter

All [options](/dynamic-maps/universal-methods/#groundoverlay-url-bounds-options) are optional.

| Option      | Type     | Description |
|-------------|----------|-------------|
| `id`        | _string_ | Reference point for each ground overlay. |
| `opacity`   | _float_  | Opacity of the image, from `0` (invisible) to `1` (solid). Defaults to `1`. |
| `clickable` | _bool_   | If `false`, the image will ignore clicks. |

:::tip No Map ID needed
Unlike [datasets and boundaries](/guides/datasets-and-boundaries/), a ground overlay works on any map.
:::

## Bringing the image into view

Adding a ground overlay will not move the map. To bring the image into view, set the map's `center` and `zoom`, or call [`fit`](/dynamic-maps/universal-methods/#fit) after adding the image...

:::code
```js
map.groundOverlay(url, bounds).fit();
```
```twig
{% do map.groundOverlay(url, bounds).fit() %}
```
```php
$map->groundOverlay($url, $bounds)->fit();
```
:::

## Hiding or showing a ground overlay

To dynamically hide or show a ground overlay, use [`hideGroundOverlay`](/dynamic-maps/universal-methods/#hidegroundoverlay-overlayid) and [`showGroundOverlay`](/dynamic-maps/universal-methods/#showgroundoverlay-overlayid)...

:::code
```js
// Hide the ground overlay
map.hideGroundOverlay('my-overlay');

// Show the ground overlay
map.showGroundOverlay('my-overlay');
```
```twig
{# Hide the ground overlay #}
{% do map.hideGroundOverlay('my-overlay') %}

{# Show the ground overlay #}
{% do map.showGroundOverlay('my-overlay') %}
```
```php
// Hide the ground overlay
$map->hideGroundOverlay('my-overlay');

// Show the ground overlay
$map->showGroundOverlay('my-overlay');
```
:::

### Beyond hiding and showing

When you need to do more than hide or show an image, you can get the raw [Google Ground Overlay object](https://developers.google.com/maps/documentation/javascript/reference/image-overlay#GroundOverlay), and manipulate it further using Google's own API.

This method is only available [in JavaScript](/dynamic-maps/javascript-methods/#getgroundoverlay-overlayid).

```js
// Get the raw Google Ground Overlay object
const overlay = map.getGroundOverlay('my-overlay');
```
