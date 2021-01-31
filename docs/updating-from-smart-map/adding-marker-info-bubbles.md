# 🔧 Adding marker info bubbles

<update-message/>

Updating this is very straightforward, simply change the name of the [option](/models/dynamic-map-model/#construct-locations-options) which specifies the Twig template. The underlying behavior remains nearly identical.

```twig
{# OLD #}
{% set options = {
    'markerInfo': 'example/my-info-window'
} %}

{# NEW #}
{% set options = {
    'infoWindowTemplate': 'example/my-info-window'
} %}
```

:::tip New Documentation
See the complete new [Info Windows](/dynamic-maps/info-windows/) documentation.
:::
