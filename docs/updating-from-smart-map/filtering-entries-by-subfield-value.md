# 🔧 Filtering entries by subfield value

<update-message/>

In the new Google Maps plugin, this [option](/proximity-search/options/#subfields) has been renamed from `filter` to `subfields`.

Other than being renamed, there is no functional difference in behavior.

```twig
{# OLD #}
{% set options = {
    'filter': {
        'country': 'United States'
    }
} %}

{# NEW #}
{% set options = {
    'subfields': {
        'country': 'United States'
    }
} %}
```

:::tip New Documentation
See the complete new [Filter by Subfields](/guides/filter-by-subfields/) documentation.
:::
