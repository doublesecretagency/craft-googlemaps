---
description:
---

# KML Layers

When the general tools of Google Maps are not enough, you can always turn to [KML layers](https://developers.google.com/maps/documentation/javascript/examples/layer-kml) in order to "kick it up a notch". Within the context of a KML layer, the options to decorate a map are practically boundless.

There are a few tools for creating a KML layer, here's the official Google resource:
 - [Google My Maps](https://www.google.com/maps/about/mymaps/)

However you go about creating your KML layer, it's easy to apply it to an existing map...

:::code
```js
map.kml(url, options);
```
```twig
{% do map.kml(url, options) %}
```
```php
$map->kml($url, $options);
```
:::

Practically speaking, your code could look something like this...

:::code
```js
// Create a plain dynamic map
var map = googleMaps.map();

// Add a KML layer to the map
map.kml(url);
```
```twig
{# Create a plain dynamic map #}
{% set map = googleMaps.map() %}

{# Add a KML layer to the map #}
{% do map.kml(url) %}
```
```php
// Create a plain dynamic map
$map = GoogleMaps::map();

// Add a KML layer to the map
$map->kml($url);
```
:::

For more information, take a look at the details of the [`kml` method](/dynamic-maps/universal-methods/#kml-url-options).

## The `url` parameter

The most important things to note about the `url` parameter:

 - You must use a complete URL (including the `http(s)://` prefix).
 - You must use a publicly accessible URL (because Google needs to parse it).

:::warning URL must be publicly accessible
When Google loads the map, it needs to internalize and process the KML file. Because of this, it's not possible for Google to parse KML files that are not publically accessible.

If you are testing this feature locally, the KML file may [refuse to load](https://stackoverflow.com/a/3515444/3467557).
:::

## The `options` parameter

All options are optional. You can specify any attributes of an [`KmlLayerOptions`](https://developers.google.com/maps/documentation/javascript/reference/kml#KmlLayerOptions) interface.

:::code
```js
map.kml(url, {
    'preserveViewport': true
});
```
```twig
{% do map.kml(url, {
    'preserveViewport': true
}) %}
```
```php
$map->kml($url, [
    'preserveViewport' => true
]);
```
:::

:::tip Map & URL automatically determined
Within the context of the KML layer options, both the `map` and `url` values will be set automatically.
:::
