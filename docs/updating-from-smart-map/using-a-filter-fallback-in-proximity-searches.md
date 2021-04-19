# 🔧 Using a filter fallback in proximity searches

<update-message/>

In the new Google Maps plugin, this [option](/proximity-search/options/#subfields) has been renamed from `filter` to `subfields`.

Other than being renamed, there is no functional difference in behavior.

```twig
{# OLD #}
{% set options = {
    'target': 'California',
    'filter': 'fallback'
} %}

{# NEW #}
{% set options = {
    'target': 'California',
    'subfields': 'fallback'
} %}
```

:::tip New Documentation
See the complete new [Subfield Filter Fallback](/guides/filter-by-subfields/#subfield-filter-fallback) documentation.
:::
