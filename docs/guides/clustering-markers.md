# Clustering Markers

## Example

Before any further explanation, here is the general snippet from which you can copy & paste to add marker clustering to your [dynamic map](/dynamic-maps/). It will likely require some minor adjustments for your site.

```twig
{# Load the marker clustering library #}
{% js 'https://unpkg.com/@googlemaps/markerclustererplus/dist/index.min.js' %}

{# Create the JS callback function #}
{% js %}

    // Add marker clustering
    function addClustering() {

        // Get the map object
        var mapObject = googleMaps.getMap('my-map');

        // Get map & markers
        var map = mapObject._map;
        var markers = Object.values(mapObject._markers);

        // Cluster markers
        new MarkerClusterer(map, markers, {
            imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
        });

    }
    
{% endjs %}

{# Specify callback function in map options #}
{% set options = {
    id: 'my-map',
    callback: 'addClustering',
} %}

{# Get locations #}
{% set locations = craft.entries.section('locations').all() %}

{# Display the map #}
{{ googleMaps.map(locations, options).tag() }}
```

## Instructions

1. First, you must **load the marker clustering library**. The example above points to a CDN, but you could alternatively store a local copy of the library.

2. You then need to **create the JS callback function**. If desired, this function could be stored in a separate `.js` file. Be sure it gets loaded _after_ the plugin loads [the `googlemaps.js` file](/javascript/googlemaps.js/).

3. **Specify the `callback` function in the map's [options](/models/dynamic-map-model/#construct-locations-options)**. If you are referencing a named function, simply pass the name of the function. It's also possible to pass an anonymous function (as a string).

4. Lastly, you'll want to **get the locations** and **display the map**. It's very likely that you already have this part worked out. If not, check out the documentation for [creating a dynamic map...](/dynamic-maps/)
