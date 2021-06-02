---
description:
---

# 🔧 Filtering out entries with invalid coordinates

<update-message/>

In the new Google Maps plugin, this [option](/proximity-search/options/#requirecoords) has been renamed from `hasCoords` to `requireCoords`.

Other than being renamed, there is no functional difference in behavior.

```twig
{# OLD #}
{% set options = {
    'hasCoords': true
} %}

{# NEW #}
{% set options = {
    'requireCoords': true
} %}
```

:::tip New Documentation
See the complete new [Proximity Search Options](/proximity-search/options/) documentation.
:::
