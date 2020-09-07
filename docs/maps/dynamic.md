# Dynamic Maps

## Examples

### Simple:

```twig
{# Get all locations #}
{% set locations = craft.entries.section('locations').all() %}

{# Display them on a map #}
{{ googleMaps.map(locations).html() }}
```

### Complex:

```twig
{# Build a map with two sets of locations and a KML file #}
{% set map = googleMaps.map(locations, mapOptions)
    .markers(moreLocations, markerOptions)
    .kml(filename)
%}

{# Render map #}
{{ map.html() }}
```

:::warning The Magic of Chaining
With only a couple of exceptions (`map` and `html`), virtually all methods listed below can be chained in any order you'd like. Chaining can be a powerful technique, allowing you to build complex maps with ease.

 To get a better understanding of how it works, read more on [Chaining Methods](/maps/chaining/).
:::

## Chaining

The `map` method will generate a [Dynamic Map Model](/models/dynamic-map-model/). You can create a dynamic map containing a set of markers, then render the `<div>` container tag.

There are several methods on the Dynamic Map Model that can be chained together. When combining these methods, it will be possible for you to mix & match information in order to build the map in any way you please.

For complete information, see the page regarding [Chaining...](/maps/chaining/)

