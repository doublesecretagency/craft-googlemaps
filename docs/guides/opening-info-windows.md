# Opening Info Windows

If your map includes [info windows](/dynamic-maps/info-windows/), you typically open them by simply clicking on each corresponding marker. Sometimes, however, you'll need something _outside of_ the map to open an info window.

Here's an example of how you might create a **list of locations** that correspond with **markers on a map**. Clicking on the name of each location will open its corresponding info window. As an added bonus, the example below will also [pan to the marker](/dynamic-maps/universal-methods/#pantomarker-markerid) and [zoom the map](/dynamic-maps/universal-methods/#zoom-level).

:::warning Map ID
When working with complex JavaScript, it's recommended to set an `id` for your map. This makes it much easier for you to refer back to the map and continue manipulating it.
:::

:::code
```twig
{# Configure map options #}
{% set options = {
    'id': 'my-map',
    'infoWindowTemplate': 'example/my-info-window'
} %}

{# Display a map with all locations #}
{{ googleMaps.map(entries, options).tag() }}

{# Display a list of locations, click each to open info window #}
<ul>
    {% for entry in entries %}
        <li onclick="openInfoWindow('my-map', 'myAddressField', {{ entry.id }})">
            {{ entry.title }} - {{ entry.address.distance }} miles away
        </li>
    {% endfor %}
</ul>
```
:::

The Twig snippet above will display:

 - A **map** containing all markers.
 - A **list** of all locations.

The list of locations contains a JavaScript trigger to `openInfoWindow`...

:::code
```js
// Open a specified info window
function openInfoWindow(mapId, fieldHandle, entryId) {

    // Compile the marker ID (doubles as info window ID)
    var markerId = `${entryId}-${fieldHandle}`;

    // Get the map, marker, and info window objects
    var map        = googleMaps.getMap(mapId);
    var marker     = map.getMarker(markerId);
    var infoWindow = map.getInfoWindow(markerId);

    // Close all open info windows
    for (var i in map._infoWindows) {
        map._infoWindows[i].close();
    }

    // Open the specified info window
    infoWindow.open(map._map, marker);

    // Pan to marker and zoom in
    map.panToMarker(markerId).zoom(9);

}
```
:::

:::tip What's the difference?
You may have noticed two similar (but different) JavaScript objects in this example. They can both be referred to as "map objects", so it can be a little confusing sometimes.

`map._map` - The **Google** map object. This is the raw Google Map object that Google creates and manages.

`map` - The **plugin's** map object. Contains a single Google Map object, plus any Google Markers and Google Info Windows attached to it. In addition to serving as a wrapper for Google objects, it also provides a large [set of methods](/dynamic-maps/universal-methods/) for complex map management.
:::
