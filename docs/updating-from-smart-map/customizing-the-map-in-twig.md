# 🔧 Customizing the map in Twig

<update-message/>

The underlying premise is the same: Specify a set of `options` as the second parameter when creating the map.

That being said, the available [options](/models/dynamic-map-model/#construct-locations-options) have changed significantly. Please take a closer look to see how your map configuration may need to be adjusted.

```twig
{# OLD #}
{{ craft.smartMap.map(locations, options) }}

{# NEW #}
{{ googleMaps.map(locations, options).tag() }}
```

:::tip New Documentation
See the complete new [Dynamic Map Options](/models/dynamic-map-model/#construct-locations-options) documentation.
:::
