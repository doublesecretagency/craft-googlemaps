---
description:
---

# Setting the Map Height

One of the most common mistakes when rendering a map is when you accidentally create a map that is **zero pixels tall**. This will, of course, hide the map entirely. To see the map as intended, make sure to specify a height for the map container.

:::warning Enable devMode
Make sure to enable the [`devMode` config setting](https://craftcms.com/docs/3.x/config/config-settings.html#devmode) when developing your maps. You will see a much more detailed log of the map creation process, which will warn you if there are any issues with the map height.
:::

## Via CSS

Using CSS to manage the map height is arguably the easiest approach.

```css
.gm-map {
    height: 320px;
}
```

As you can see, there is a universal `gm-map` class which exists on all map containers. Additionally, if you specified an `id` when creating the map, you could also refer to the container that way...

```css
#my-map-id {
    height: 320px;
}
```

## Via JS, Twig, or PHP

The other recommended way to control the map height is to set it when the map is initially created. Regardless of which language you are working with, you can specify the `height` option of the map.

:::code
```js
const map = googleMaps.map(locations, {
    'height': 320
});
```
```twig
{% set map = googleMaps.map(locations, {
    'height': 320
}) %}
```
```php
$map = GoogleMaps::map($locations, [
    'height' => 320
]);
```
:::

Take a look at the [available options](/dynamic-maps/basic-map-management/#map-locations-options) for creating a dynamic map.
