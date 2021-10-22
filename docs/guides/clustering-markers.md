---
description:
---

# Clustering Markers

<img class="dropshadow" :src="$withBase('/images/guides/clustering-markers.png')" alt="Example of clustered markers on a dynamic map">

Marker clustering is a great way to place a lot of markers onto a single map without overcrowding the map viewing area. It's easy to add [marker clustering](https://developers.google.com/maps/documentation/javascript/marker-clustering) to your maps. When creating a map, simply set the [`cluster` option](/dynamic-maps/basic-map-management/#dynamic-map-options) to one of the following values:

- `true` - Enables the default marker clustering behavior.
- An **array** of [MarkerClustererOptions](https://googlemaps.github.io/js-markerclustererplus/interfaces/markerclustereroptions.html) - Enables a custom marker clustering behavior.

:::tip Disabled by default
By default, marker clustering is set to `false` (disabled).
:::

## Basic Clustering

To enable the default clustering behavior, simply set `cluster` to `true` when creating a map.

:::code
```js
// Enable basic marker clustering
var options = {
    'cluster': true
};

// Create a map with marker clusters
var map = googleMaps.map(locations, options);
```
```twig
{# Enable basic marker clustering #}
{% set options = {
    'cluster': true
} %}

{# Create a map with marker clusters #}
{% set map = googleMaps.map(locations, options) %}
```
```php
// Enable basic marker clustering
$options = [
    'cluster' => true
];

// Create a map with marker clusters
$map = GoogleMaps::map($locations, $options);
```
:::


:::warning Required JS Assets
If your map is exclusively using JavaScript, the marker clustering icon path will not be loaded automatically. Please ensure the [required JS assets](/guides/required-js-assets/) are being properly loaded.
:::

## Advanced Clustering

If you need further customization over the marker clustering, you can use an array instead. The array would be a set of [Marker Clusterer Options](https://googlemaps.github.io/js-markerclustererplus/interfaces/markerclustereroptions.html) which allow you to further customize the marker clustering behavior.

:::code
```js
// Enable advanced marker clustering
var options = {
    'cluster': {
        'imagePath': 'https://example.com/path/to/cluster-icons'
    }
};

// Create a map with marker clusters
var map = googleMaps.map(locations, options);
```
```twig
{# Enable advanced marker clustering #}
{% set options = {
    'cluster': {
        'imagePath': 'https://example.com/path/to/cluster-icons'
    }
} %}

{# Create a map with marker clusters #}
{% set map = googleMaps.map(locations, options) %}
```
```php
// Enable advanced marker clustering
$options = [
    'cluster' => [
        'imagePath' => 'https://example.com/path/to/cluster-icons'
    ]
];

// Create a map with marker clusters
$map = GoogleMaps::map($locations, $options);
```
:::

The example above shows how to set your own custom clustering icons.

## Custom Clustering Icons

To use your own collection of clustering icons, set the [`imagePath` marker clusterer option](https://googlemaps.github.io/js-markerclustererplus/interfaces/markerclustereroptions.html#imagepath).

:::code
```js
'cluster': {
    'imagePath': 'https://example.com/path/to/cluster-icons'
}
```
```twig
'cluster': {
    'imagePath': 'https://example.com/path/to/cluster-icons'
}
```
```php
'cluster' => [
    'imagePath' => 'https://example.com/path/to/cluster-icons'
]
```
:::

It should be a **path to the group of image files** to use for cluster icons. For example, let's say you store five clustering marker icons (`m1.png` - `m5.png`) in the following public directory:

```
https://example.com/resources/images/clustering
```

In that case, you would set the `imagePath` value to this:

```
https://example.com/resources/images/clustering/m
```

The icons path can be absolute or relative, even in a local development environment.

```
/resources/images/clustering/m
```

:::tip More Info
For more information, see the official Google docs for [adding a marker clusterer](https://developers.google.com/maps/documentation/javascript/marker-clustering#adding-a-marker-clusterer).
:::

## Get the Marker Clustering Object

In JavaScript, you can get the marker clustering object of an existing map:

```js
// Get the marker clustering object
var markerCluster = map.getMarkerCluster();
```

This provides you with a [MarkerClusterer object](https://googlemaps.github.io/js-markerclustererplus/classes/default.html). What you choose to do with it is entirely up to you.

```js
// When the user moves the cursor over a cluster
google.maps.event.addListener(markerCluster, 'mouseover', function (c) {
    console.log('Cluster mouseover: ');
    console.log('- Markers in cluster: ' + c.getSize());
    console.log('- Center: ' + c.getCenter());
});

// When the user moves the cursor away from a cluster
google.maps.event.addListener(markerCluster, 'mouseout', function (c) {
    console.log('Cluster mouseout: ');
    console.log('- Markers in cluster: ' + c.getSize());
    console.log('- Center: ' + c.getCenter());
});

// When the user clicks on a cluster
google.maps.event.addListener(markerCluster, 'click', function (c) {
    console.log('Cluster click: ');
    console.log('- Markers in cluster: ' + c.getSize());
    console.log('- Center: ' + c.getCenter());
    var m = c.getMarkers();
    var p = [];
    for (var i = 0; i < m.length; i++) {
        p.push(m[i].getPosition());
    }
    console.log('- Marker locations: ' + p.join(', '));
});
```
