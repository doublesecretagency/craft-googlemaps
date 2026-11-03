---
description: Draw custom shapes onto a map using GeoJSON. Load the shapes from a GeoJSON file, or pass the data in directly.
---

# GeoJSON Layers

If your shapes are already in [GeoJSON](https://geojson.org/) format, you can draw them directly onto a dynamic map. GeoJSON is a common format for points, lines, and polygons, and most mapping tools are able to export it.

The `geoJson` method is part of the [Universal API](/dynamic-maps/universal-methods/#geojson-source-options), so you can use it in any available language...

:::code
```js
map.geoJson(source, options);
```
```twig
{% do map.geoJson(source, options) %}
```
```php
$map->geoJson($source, $options);
```
:::

## The `source` parameter

The `source` can be any of the following...

 - The **URL** of a GeoJSON file.
 - A **JSON string** of GeoJSON data.
 - The GeoJSON **data itself** (an object in JavaScript, a hash in Twig, or an array in PHP).

Most often, you'll be loading a GeoJSON file...

:::code
```js
// Add a GeoJSON layer to the map
map.geoJson('/files/parks.geojson', {'id': 'my-geojson'});
```
```twig
{# Add a GeoJSON layer to the map #}
{% do map.geoJson('/files/parks.geojson', {'id': 'my-geojson'}) %}
```
```php
// Add a GeoJSON layer to the map
$map->geoJson('/files/parks.geojson', ['id' => 'my-geojson']);
```
:::

The file is fetched by the visitor's browser, so relative and local URLs work just fine. However, a file hosted on another domain may not load. See [Files on another domain...](/guides/kml-layers/#files-on-another-domain)

If the GeoJSON is stored in a plain text field, you can pass the field value straight in...

```twig
{# Add the GeoJSON stored on an entry #}
{% do map.geoJson(entry.myGeoJsonField) %}
```

Or you can build the data by hand...

:::code
```js
map.geoJson({
    'type': 'Feature',
    'geometry': {
        'type': 'Point',
        'coordinates': [-64.7527469, 32.3113966]
    },
    'properties': {
        'name': 'Bermuda'
    }
});
```
```twig
{% do map.geoJson({
    'type': 'Feature',
    'geometry': {
        'type': 'Point',
        'coordinates': [-64.7527469, 32.3113966]
    },
    'properties': {
        'name': 'Bermuda'
    }
}) %}
```
```php
$map->geoJson([
    'type' => 'Feature',
    'geometry' => [
        'type' => 'Point',
        'coordinates' => [-64.7527469, 32.3113966]
    ],
    'properties' => [
        'name' => 'Bermuda'
    ]
]);
```
:::

:::warning Longitude comes first
GeoJSON coordinates are written as `[longitude, latitude]`, which is the **reverse** of how coordinates are written everywhere else in this plugin.
:::

## The `options` parameter

All [options](/dynamic-maps/universal-methods/#geojson-source-options) are optional.

| Option                | Type               | Description |
|-----------------------|--------------------|-------------|
| `id`                  | _string_           | Reference point for each GeoJSON layer. |
| `preserveViewport`    | _bool_             | If `true`, the map will not zoom to fit the layer once it loads. |
| `suppressInfoWindows` | _bool_             | If `true`, clicking a shape will not open an info window. |
| `clickable`           | _bool_             | If `false`, the layer will ignore clicks entirely (and no info windows will open). |
| `zIndex`              | _int_              | Stacking order of the layer, relative to other layers. |
| `style`               | _object_           | Any [`google.maps.Data.StyleOptions`](https://developers.google.com/maps/documentation/javascript/reference/data#Data.StyleOptions) properties. Overrides the styles from the GeoJSON data. |

Once the layer has loaded, the map will zoom to show the entire layer (unless you set `preserveViewport`).

## Styling the shapes

Each shape can carry its own style in its `properties`, using the [simplestyle](https://github.com/mapbox/simplestyle-spec) property names...

 - `stroke` - Line color.
 - `stroke-width` - Line width.
 - `stroke-opacity` - Line opacity.
 - `fill` - Fill color.
 - `fill-opacity` - Fill opacity.
 - `icon` - URL of a custom icon for a point.

To apply the same style to every shape in the layer, use the `style` option instead. It will override any styles from the data itself.

:::code
```js
map.geoJson(source, {
    'style': {
        strokeColor: '#226666',
        strokeWeight: 2,
        fillColor: '#226666',
        fillOpacity: 0.35
    }
});
```
```twig
{% do map.geoJson(source, {
    'style': {
        'strokeColor': '#226666',
        'strokeWeight': 2,
        'fillColor': '#226666',
        'fillOpacity': 0.35
    }
}) %}
```
```php
$map->geoJson($source, [
    'style' => [
        'strokeColor' => '#226666',
        'strokeWeight' => 2,
        'fillColor' => '#226666',
        'fillOpacity' => 0.35
    ]
]);
```
:::

In JavaScript, the `style` option can also be a [styling function](https://developers.google.com/maps/documentation/javascript/reference/data#Data.StylingFunction), which receives each shape and returns its style.

## Info windows

Clicking a shape will open an info window showing its `name` and `description` properties. Any HTML in the description is sanitized before it's shown.

To turn the info windows off, set `suppressInfoWindows` to `true`.

## Hiding or showing a GeoJSON layer

To dynamically hide or show a GeoJSON layer, use [`hideGeoJson`](/dynamic-maps/universal-methods/#hidegeojson-geojsonid) and [`showGeoJson`](/dynamic-maps/universal-methods/#showgeojson-geojsonid)...

:::code
```js
// Hide the GeoJSON layer
map.hideGeoJson('my-geojson');

// Show the GeoJSON layer
map.showGeoJson('my-geojson');
```
```twig
{# Hide the GeoJSON layer #}
{% do map.hideGeoJson('my-geojson') %}

{# Show the GeoJSON layer #}
{% do map.showGeoJson('my-geojson') %}
```
```php
// Hide the GeoJSON layer
$map->hideGeoJson('my-geojson');

// Show the GeoJSON layer
$map->showGeoJson('my-geojson');
```
:::

### Beyond hiding and showing

When you need to do more than hide or show a layer, you can get the raw [Google Data layer object](https://developers.google.com/maps/documentation/javascript/reference/data#Data), and manipulate it further using Google's own API.

This method is only available [in JavaScript](/dynamic-maps/javascript-methods/#getgeojson-geojsonid).

```js
// Get the raw Google Data layer object
const geoJson = map.getGeoJson('my-geojson');
```
