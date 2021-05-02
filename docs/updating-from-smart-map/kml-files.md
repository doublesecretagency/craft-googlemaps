---
description:
---

# 🔧 KML files

<update-message/>

```twig
{# OLD #}
{% set kmlFile = entry.kmlFile.one() %}
{% set options = {
    'height': 400,
    'center': {'lat':34.0522342, 'lng':-118.2436849}
} %}

{% if kmlFile %}
    {{ craft.smartMap.kml(kmlFile, options) }}
{% endif %}
```
```twig
{# NEW #}
{% set kmlFile = entry.kmlFile.one() %}
{% set options = {
    'height': 400,
    'center': {'lat':34.0522342, 'lng':-118.2436849}
} %}

{% if kmlFile and kmlFile.url %}
    {{ googleMaps.map([], options).kml(kmlFile.url).tag() }}
{% endif %}
```

At a glance, the code hasn't changed much. But internally, the KML functionality now operates much differently than it did previously.

**Major functional changes:**

- You are now creating a [map object](/dynamic-maps/basic-map-management/#map-locations-options) first, before applying the [KML layer](/dynamic-maps/universal-methods/#kml-url-options) as a separate step.
- This demonstrates the concept of [chaining](/dynamic-maps/chaining/).
- Instead of using a complete Asset, you must only pass a **valid URL** into the `kml` method.

:::tip New Documentation
See the complete new [KML Layers](/guides/kml-layers/) documentation.
:::
