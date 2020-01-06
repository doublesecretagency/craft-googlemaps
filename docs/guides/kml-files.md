# KML Files

When the needs of your map get too complex, you may find it beneficial to create a custom map with [Google My Maps](https://www.google.com/maps/about/mymaps/) (or a similar service). Once you have your KML file in hand, it's easy to load that file into your map...

```twig
{% do googleMaps.loadKml(mapId, kml) %}
```

## loadKml(mapId, kml, options = {})

 - `mapId` - The ID of a map as specified by `MAP-ID` [here](/javascript-object/google-maps-objects/).
 - `kml` - The URL or Asset of a KML file. (See [Value of `kml` parameter](#value-of-the-kml-parameter) below.)
 - `options` - Takes the form of [KmlLayerOptions](https://developers.google.com/maps/documentation/javascript/reference/kml#KmlLayerOptions).

Before you can add a KML file, you will need a map to add it to. If you just want to apply the KML file to a blank map, you can do so like this:

```twig
{# Create a blank map #}
{{ googleMaps.dynamic() }}

{# Load the KML file #}
{% do googleMaps.loadKml('gm-map-1', kml) %}
```

::: tip AN EMPTY MAP
You can configure an otherwise blank map by setting the first parameter to `null`...

```twig
{{ googleMaps.dynamic(null, options) }}
```
:::

## Value of the `kml` parameter

There are two acceptable formats for the `kml` parameter: a **string** or an **Asset**.

### `string` - A full URL pointing to a publicly accessible KML file.
 
**Use the URL of a publicly accessible KML file:**

```twig
{% set kml = 'https://example.com/path/to/file.kml' %}
```

### `Asset`- A normal Craft asset, stored either locally or in the cloud.

**Use a publicly accessible KML asset:**

```twig
{% set kml = entry.kmlAsset.one() %}
```

::: warning KML FILE MUST BE PUBLICLY ACCESSIBLE
Because of the way Google Maps applies the KML layer, the KML file must be accessible from a public website.

If you are testing this feature locally, the KML file may [refuse to load](https://stackoverflow.com/a/3515444/3467557).
:::

## Using JavaScript

## createKmlLayer(mapId, kmlUrl, options = {})

The `kmlUrl` value must be a **string**. Provide a URL to a **publicly accessible** KML file.

```js
// Create a blank map in JavaScript
googleMapsPlugin.createMap('kml-map');

// Apply a KML file to the new map
var kmlUrl = 'https://example.com/path/to/file.kml';
googleMapsPlugin.createKmlLayer('kml-map', kmlUrl);
```

See the [method definition...](/javascript-object/#createkmllayer-mapid-kmlurl-options)
