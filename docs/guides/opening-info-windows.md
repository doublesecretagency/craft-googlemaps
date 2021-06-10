---
description:
---

# Opening Info Windows

If your map includes [info windows](/dynamic-maps/info-windows/), users will typically open them by clicking on each corresponding marker. Sometimes, however, you'll want something _outside_ the map to open an info window.

The Twig snippet below will display:

1. A **dynamic map**, containing markers for all entries.
2. A **list of entries**. Clicking on each title will open its corresponding info window on the map.

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
    <div onclick="googleMaps.getMap('my-map').openInfoWindow('{{ markerId }}')">
        {{ entry.title }}
    </div>

{% endfor %}
```

The `openInfoWindow` method is available in JavaScript, Twig, and PHP. Its usage is nearly identical across all three languages, although you will have more versatility when using it in JavaScript (since Twig and PHP are only relevant when the map is first loaded).

:::tip More Info
For more information, check out the [`openInfoWindow` universal method...](/dynamic-maps/universal-methods/#openinfowindow-markerid)
:::
