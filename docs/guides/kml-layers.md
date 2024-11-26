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
const map = googleMaps.map();

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

For more information, take a look at the details of the [`kml` method](/dynamic-maps/universal-methods/#kml-url-options). If you intend to further manipulate the KML layers, it will be necessary to provide an `id` value.

## The `url` parameter

The most important things to note about the `url` parameter:

 - You must use a complete URL (including the `http(s)://` prefix).
 - You must use a publicly accessible URL (because Google needs to parse it).

:::warning URL must be publicly accessible
When Google loads the map, it needs to internalize and process the KML file. Because of this, it's not possible for Google to parse KML files that are not publically accessible.

If you are testing this feature locally, the KML file may [refuse to load](https://stackoverflow.com/a/3515444/3467557).
:::

## The `options` parameter

All [options](/dynamic-maps/universal-methods/#kml-url-options) are optional.

Within the context of `kmlLayerOptions`, you can specify any attributes of a [`KmlLayerOptions`](https://developers.google.com/maps/documentation/javascript/reference/kml#KmlLayerOptions) interface. There's no need to specify `map` or `url` values, as they will be set automatically.

:::code
```js
map.kml(url, {
    'id': 'my-kml',
    'kmlLayerOptions': {
        'preserveViewport': true
    }
});
```
```twig
{% do map.kml(url, {
    'id': 'my-kml',
    'kmlLayerOptions': {
        'preserveViewport': true
    }
}) %}
```
```php
$map->kml($url, [
    'id' => 'my-kml',
    'kmlLayerOptions' => [
        'preserveViewport' => true
    ]
]);
```
:::

:::warning Further KML Adjustments
To manipulate a KML layer, you will need to refer to it by its assigned ID. If you plan to manipulate the KML layers, be sure to set the `id` option of each KML layer when initially adding it to the map.
:::

## Further manipulating KML layers

If you only need to hide or show each KML layer, there are two convenient [universal methods](/dynamic-maps/universal-methods/#hidekml-kmlid)...

:::code
```js
// Hide the KML layer
map.hideKml('my-kml');

// Show the KML layer
map.showKml('my-kml');
```
```twig
// Hide the KML layer
{% do map.hideKml('my-kml') %}

// Show the KML layer
{% do map.showKml('my-kml') %}
```
```php
// Hide the KML layer
$map->hideKml('my-kml');

// Show the KML layer
$map->showKml('my-kml');
```
:::

### Beyond hiding and showing

When you need to do more than hide or show a layer, you can get the raw [Google KML layer object](https://developers.google.com/maps/documentation/javascript/reference/kml#KmlLayer), and manipulate it further using Google's own API.

This method is only available [in JavaScript](/dynamic-maps/javascript-methods/#getkml-kmlid).

```js
// Get the raw Google KML layer object
const kml = map.getKml('my-kml');
```
