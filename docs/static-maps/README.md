# Static Maps

Whether you are working in Twig or PHP, the syntax is nearly identical. Unlike the [Universal API](/dynamic-maps/api/) for dynamic maps, there is no JavaScript equivalent for static maps.

## Creating a Map in Twig

Here's a straightforward example of how you might create a simple static map in Twig...

```twig
{# Get all locations #}
{% set locations = craft.entries.section('locations').all() %}

{# Create a static map with markers #}
{{ googleMaps.img(locations).tag() }}
```

Of course, that is only the beginning. You can chain additional methods to produce far more complex maps. Take a look at the [`StaticMap` model](/models/static-map-model/) to see all the methods available for chaining.

## Configuring the Map

By chaining methods together, you can build a map into whatever form necessary.

```twig
{# Create a blank map
 #   Add locations with green markers
 #   Draw a line between the locations
 #   Render the <img> tag
 #}
{{ googleMaps.img()
    .markers(locations, {'color':'green'})
    .path(locations)
    .tag()
}}
```

This gives you the maximum amount of control over how each map will be rendered.

## Location Variations

When creating a new map, or appending markers to an existing map, you'll have an opportunity to provide a set of [locations](/dynamic-maps/locations/) for placement on the map.
