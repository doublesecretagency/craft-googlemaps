---
description:
---

# Clustering Markers

To implement [marker clustering](https://developers.google.com/maps/documentation/javascript/marker-clustering), an additional JavaScript library is required.

## Example

Before any further explanation, here is the general snippet from which you can copy & paste to add marker clustering to your [dynamic map](/dynamic-maps/). It will likely require some minor adjustments for your site.

```twig
{# 1. Load the marker clustering library #}
{% js 'https://unpkg.com/@googlemaps/markerclustererplus/dist/index.min.js' %}

{# 2. Create the JS callback function #}
{% js %}
    // Add marker clustering
    function addClustering() {
        // Get the map object
        var myMap = googleMaps.getMap('my-map');
        // Get map & markers
        var map = myMap._map;
        var markers = Object.values(myMap._markers);
        // Cluster markers
        new MarkerClusterer(map, markers, {
            imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
        });
    }
{% endjs %}

{# 3. Get all locations to appear on the map #}
{% set locations = craft.entries.section('locations').all() %}

{# 4. Specify the map ID #}
{% set mapOptions = {
    'id': 'my-map'
} %}

{# 5. Specify the JS callback function #}
{% set tagOptions = {
    'callback': 'addClustering'
} %}

{# 6. Display the map #}
{{ googleMaps.map(locations, mapOptions).tag(tagOptions) }}
```

## Instructions

1. First, you must **load the marker clustering library**. The example above points to a CDN, but you could alternatively store a local copy of the library.

2. You then need to **create the JS callback function**. If desired, this function could be stored in a separate `.js` file. Be sure it gets loaded _after_ the plugin loads the [`googlemaps.js` file](/javascript/googlemaps.js/).

3. **Get the locations**, just as you normally would.  It's very likely that you already have this part worked out. If not, check out the documentation for [creating a dynamic map...](/dynamic-maps/)

4. **Specify the map's `id`** in the [`map` options](/dynamic-maps/basic-map-management/#map-locations-options). This makes it easy to reference the map in JavaScript.

5. **Specify the `callback` function** in the [`tag` options](/dynamic-maps/twig-php-methods/#tag-options). If you are referencing a named function, specify the name of the function. Or you can pass an anonymous function (as a _string_ in Twig/PHP).

6. Lastly, **display the map** by providing the `locations`, `mapOptions`, and `tagOptions`.
