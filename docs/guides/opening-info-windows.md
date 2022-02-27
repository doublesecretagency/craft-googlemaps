---
description:
---

# Opening Info Windows

If your map includes [info windows](/dynamic-maps/info-windows/), users will typically open them by clicking on each corresponding marker. Sometimes, however, you'll want something _outside_ of the map to open an info window.

The snippet below includes three parts:

1. A **dynamic map**, containing markers for all entries.
2. A **list of entries**. Clicking on each title will open its corresponding info window on the map.
3. The **JavaScript function** which handles switching between info windows.

```twig
{# Configure the map options #}
{% set options = {
    'id': 'my-map',
    'infoWindowTemplate': 'example/my-info-window'
} %}

{# Display a dynamic map with all entries #}
{{ googleMaps.map(entries, options).tag() }}

{# Loop through all entries #}
{% for entry in entries %}

    {# Compile each marker ID #}
    {% set markerId = "#{entry.id}-myAddressField" %}

    {# On click, open the info window of the specified marker #}
    <div onclick="infoWindow('{{ markerId }}')">
        {{ entry.title }}
    </div>

{% endfor %}

{# JavaScript function to switch info windows #}
<script>
    function infoWindow(markerId) {
        googleMaps.getMap('my-map')    // Get the map
            .closeInfoWindow('*')      // Close all info windows
            .openInfoWindow(markerId); // Open the specified info window
    }
</script>
```

The `openInfoWindow` and `closeInfoWindow` methods are available in JS, Twig, and PHP.

Usage is nearly identical across all three languages, although you will have more versatility when using it in **JavaScript** (since Twig and PHP are only relevant when the map is first loaded).

:::tip Further Reading
For more information, see the [`openInfoWindow` and `closeInfoWindow` universal methods...](/dynamic-maps/universal-methods/#openinfowindow-markerid)
:::
