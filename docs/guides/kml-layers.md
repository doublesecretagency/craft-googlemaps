---
description: When the general tools of Google Maps are not enough, you can always turn to KML layers. Within the context of a KML layer, the options to decorate a map are practically boundless.
meta:
- property: og:type
  content: website
- property: og:url
  content: https://plugins.doublesecretagency.com/google-maps/guides/kml-layers/
- property: og:title
  content: KML Layers | Google Maps plugin for Craft CMS
- property: og:description
  content: When the general tools of Google Maps are not enough, you can always turn to KML layers. Within the context of a KML layer, the options to decorate a map are practically boundless.
- property: og:image
  content: https://plugins.doublesecretagency.com/google-maps/images/guides/kml-chicago-subway.png
- property: twitter:card
  content: summary_large_image
- property: twitter:url
  content: https://plugins.doublesecretagency.com/google-maps/guides/kml-layers/
- property: twitter:title
  content: KML Layers | Google Maps plugin for Craft CMS
- property: twitter:description
  content: When the general tools of Google Maps are not enough, you can always turn to KML layers. Within the context of a KML layer, the options to decorate a map are practically boundless.
- property: twitter:image
  content: https://plugins.doublesecretagency.com/google-maps/images/guides/kml-chicago-subway.png
---

# KML Layers

When the general tools of Google Maps are not enough, you can always turn to [KML layers](https://developers.google.com/kml/documentation/) in order to "kick it up a notch". Within the context of a KML layer, the options to decorate a map are practically boundless.

Here is a map of the **Chicago subway system** using a KML layer...

<img class="dropshadow" :src="$withBase('/images/guides/kml-chicago-subway.png')" alt="Screenshot of a KML layer showing a map of the Chicago subway system" style="max-width:764px; margin-top:4px;">

## Which method should I use?

KML is only one of several ways to draw on a map. Depending on what you want to show, a different method may be a better fit...

| To show...                                   | Use |
|:---------------------------------------------|:----|
| Custom shapes (routes, regions, landmarks)   | [`kml`](#adding-a-kml-layer) or [`geoJson`](/guides/geojson-layers/) |
| GPS tracks (a hike, a bike ride, a run)      | [`gpx`](/guides/gpx-tracks/) |
| An image (a floor plan, a historic map)      | [`groundOverlay`](/guides/ground-overlays/) |
| Countries, states, cities, or ZIP codes      | [`boundaries`](/guides/datasets-and-boundaries/) |
| A large set of shapes uploaded to Google     | [`dataset`](/guides/datasets-and-boundaries/) |

When in doubt between `kml` and `geoJson`, just use whichever format your file is already in.

:::warning KML Is Still Supported
Google officially dropped KML support in version 3.66 of the Maps JavaScript API, but we have added backwards-compatible support to the Google Maps plugin for Craft. If you were already using KML layers, you may continue to safely do so.
:::

## Adding a KML layer

There are a few tools for creating KML files, here's the official Google resource:
 - [Google My Maps](https://www.google.com/maps/about/mymaps/)

However you go about creating your KML file, it's easy to apply it to an existing map...

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

**KMZ files work too.** A KMZ file is simply a zipped KML file, and it will be unzipped automatically.

## How it works

Once the map loads, the visitor's browser fetches the KML file, converts it, and draws it onto the map as a [Data layer](https://developers.google.com/maps/documentation/javascript/datalayer).

 - **Styles carry over.** Line colors, fill colors, line widths, and icons from the KML file are all applied.
 - **Info windows open on click.** Clicking a shape shows its name and description. Any HTML in the description is sanitized first.
 - **The map fits itself to the layer.** Once the file has loaded, the map will zoom to show the entire layer (unless you set `preserveViewport`).

The tools for reading KML and KMZ files are bundled with the plugin, and only load when a map actually needs them. Nothing is pulled from a third-party CDN, so a strict Content Security Policy won't get in the way.

## The `url` parameter

Because the browser fetches the file directly, the KML file can live almost anywhere:

 - A **complete URL** (including the `http(s)://` prefix) works.
 - A **relative URL** (like `/files/subway.kml`) works.
 - A **local URL** works, so you can test KML layers on your local development site.

The one exception is a file which lives on _another_ domain...

## Files on another domain

If the KML file is hosted on a different domain, that server must allow [cross-origin requests](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS) by sending an `Access-Control-Allow-Origin` header. If it doesn't, the browser will refuse to load the file, and the JavaScript console will show a `[GM]` error mentioning CORS.

**The easiest fix is to host a copy of the file on your own site.** For example, you could upload it as an Asset, then pass the Asset's URL into the `kml` method.

The same rule applies to [GeoJSON](/guides/geojson-layers/) and [GPX](/guides/gpx-tracks/) files.

:::tip Google My Maps
Maps made with [Google My Maps](https://www.google.com/maps/about/mymaps/) work as-is, because Google allows cross-origin requests for them. Share the map publicly, then pass its KML link into the `kml` method:

```
https://www.google.com/maps/d/kml?mid=YOUR_MAP_ID
```

The map ID is the `mid` value in the map's own URL. Google sends that link as a KMZ file, which the plugin unpacks automatically.
:::

## The `options` parameter

All [options](/dynamic-maps/universal-methods/#kml-url-options) are optional.

| Option                | Type               | Description |
|-----------------------|--------------------|-------------|
| `id`                  | _string_           | Reference point for each KML layer. |
| `preserveViewport`    | _bool_             | If `true`, the map will not zoom to fit the layer once it loads. |
| `suppressInfoWindows` | _bool_             | If `true`, clicking a shape will not open an info window. |
| `clickable`           | _bool_             | If `false`, the layer will ignore clicks entirely (and no info windows will open). |
| `zIndex`              | _int_              | Stacking order of the layer, relative to other layers. |
| `style`               | _object_           | Any [`google.maps.Data.StyleOptions`](https://developers.google.com/maps/documentation/javascript/reference/data#Data.StyleOptions) properties. Overrides the styles from the KML file. |

There's no need to specify `map` or `url` values, as they will be set automatically.

:::code
```js
map.kml(url, {
    'id': 'my-kml',
    'preserveViewport': true
});
```
```twig
{% do map.kml(url, {
    'id': 'my-kml',
    'preserveViewport': true
}) %}
```
```php
$map->kml($url, [
    'id' => 'my-kml',
    'preserveViewport' => true
]);
```
:::

:::tip Nested options work too
Each of these options can also be nested within a `kmlLayerOptions` value, so existing code written for Google's [`KmlLayerOptions`](https://developers.google.com/maps/documentation/javascript/reference/kml#KmlLayerOptions) will keep working. If an option is set both ways, the nested value wins.
:::

In JavaScript, the `style` option can also be a [styling function](https://developers.google.com/maps/documentation/javascript/reference/data#Data.StylingFunction), which receives each shape and returns its style.

:::warning Further KML Adjustments
To manipulate a KML layer, you will need to refer to it by its assigned ID. If you plan to manipulate the KML layers, be sure to set the `id` option of each KML layer when initially adding it to the map.
:::

## Known limitations

Most KML files will display exactly as expected. However, a few KML elements can't be shown on a dynamic map, and will be skipped:

 - `<NetworkLink>` (a link to another KML file)
 - `<ScreenOverlay>`
 - `<PhotoOverlay>`
 - `<Model>` (3D models)

Each skipped element will be named in a `[GM]` warning in the JavaScript console. If your file relies on a `<NetworkLink>`, you can load the linked file directly with its own `kml` call.

Regions (level of detail) are also ignored, so every shape will always be visible regardless of the zoom level.

A `<GroundOverlay>` _is_ supported, and will be shown as an image on the map. For more on placing images, see the guide for [Ground Overlays...](/guides/ground-overlays/)

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
{# Hide the KML layer #}
{% do map.hideKml('my-kml') %}

{# Show the KML layer #}
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

When you need to do more than hide or show a layer, you can get the raw [Google Data layer object](https://developers.google.com/maps/documentation/javascript/reference/data#Data), and manipulate it further using Google's own API.

This method is only available [in JavaScript](/dynamic-maps/javascript-methods/#getkml-kmlid).

```js
// Get the raw Google Data layer object
const kml = map.getKml('my-kml');
```

:::warning Returns a Data layer
The `getKml` method returns a [`google.maps.Data`](https://developers.google.com/maps/documentation/javascript/reference/data#Data) object, not a `google.maps.KmlLayer`. If your own JavaScript calls any `KmlLayer` methods directly, it will need to be updated to work with a `Data` object instead.
:::
