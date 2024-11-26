---
description:
---

# Clustering Markers

<img class="dropshadow" :src="$withBase('/images/maps/clustering-markers.png')" alt="Example of clustered markers on a dynamic map" style="margin-bottom:9px">

[Marker clustering](https://googlemaps.github.io/js-markerclusterer/) is a great way to place a lot of markers onto a single map without overcrowding the map viewing area. When creating a map, simply set the [`cluster` option](/dynamic-maps/basic-map-management/#dynamic-map-options) to one of the following values:

| Clustering Behavior            | Value of `cluster`
|:-------------------------------|:-------------------
| Off                            | `false` _(default)_
| [Default](#basic-clustering)   | `true`
| [Custom](#advanced-clustering) | Array of [Marker Clusterer Options](#marker-clusterer-options)

## Basic Clustering

To enable the default clustering behavior, simply set `cluster` to `true` when creating a map.

:::code
```js
const options = {
    'cluster': true
};
```
```twig
{% set options = {
    'cluster': true
} %}
```
```php
$options = [
    'cluster' => true
];
```
:::

This produces a standard set of marker clusters, as demonstrated in the screenshot [above](#).

## Advanced Clustering

If you need a more customized look or behavior, you can use an array of **marker clusterer options**. This gives you additional control over several things, including the clustering algorithm, the cluster icon renderer, and/or the cluster click behavior.

### Marker Clusterer Options

For more information about these options see the official [MarkerClustererOptions](https://googlemaps.github.io/js-markerclusterer/interfaces/MarkerClustererOptions.html) documentation.

| Option           | Description | Default
|:-----------------|:------------|:--------
| `algorithm`      | Function to determine how markers are clustered. | [SuperClusterAlgorithm](https://googlemaps.github.io/js-markerclusterer/classes/SuperClusterAlgorithm.html)
| `renderer`       | Function to render each cluster as a marker. | [DefaultRenderer](https://googlemaps.github.io/js-markerclusterer/classes/DefaultRenderer.html)
| `onClusterClick` | Callback function to handle individual cluster clicks. | [defaultOnClusterClickHandler](https://googlemaps.github.io/js-markerclusterer/modules.html#defaultOnClusterClickHandler)

:::code
```js
// Set a custom cluster rendering function
const options = {
    'cluster': {
        'renderer': CustomRenderer
    }
};
```
```twig
{# Set a custom cluster rendering function #}
{% set options = {
    'cluster': {
        'renderer': 'CustomRenderer'
    }
} %}
```
```php
// Set a custom cluster rendering function
$options = [
    'cluster' => [
        'renderer' => 'CustomRenderer'
    ]
];
```
:::

## Custom Clustering Icons

To see a complete example for using custom clustering icons, along with a handy downloadable JavaScript file, check out the guide for [Setting Clustering Icons...](/guides/setting-clustering-icons/)

## Get the Marker Clustering Object

In JavaScript, you can get the marker clustering object of an existing map:

```js
// Get the marker clustering object
const clusterer = map.getMarkerClusterer();
```

This provides you with a [MarkerClusterer object](https://googlemaps.github.io/js-markerclusterer/classes/MarkerClusterer.html). What you choose to do with it is entirely up to you.
