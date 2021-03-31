# 🔧 How to use with a Matrix field

<update-message/>

Matrix field usage is nearly identical to how it was handled in Smart Map, with only one exception:

 - You can no longer use a _query object_ to plot markers on a map. The query must now be explicitly converted into an **array of elements**, or any other [valid set of locations](/dynamic-maps/locations/).

```twig
{# OLD #}
{% set locations = entry.myMatrixField %}
{{ craft.smartMap.map(locations) }}

{# NEW #}
{% set locations = entry.myMatrixField.all() %}
{{ googleMaps.map(locations).tag() }}
```

:::tip New Documentation
See the complete new [Address in a Matrix Field](/guides/address-in-a-matrix-field/) documentation.
:::
