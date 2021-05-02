---
description:
---

# 🔧 Region Biasing

<update-message/>

While the functionality remains nearly identical, the organization of these parameters has changed. The `components` value must now be **nested inside the `target` array**.

Internally, the `target` parameter is fed into the [Geocoding `lookup` method](/geocoding/target/). If you look closely at that method, you'll notice that it accepts either a **string** or **array** value. You can pass in an array of data to fine-tune the geocoding mechanism.

Assuming you are passing an array into the `target` option, use the `address` sub-parameter to specify the address string for geocoding.

```twig
{# OLD #}
{% set options = {
    'target': 'Venice',
    'components': {
        'country': 'US',
        'administrative_area': 'California',
    },
} %}

{# NEW #}
{% set options = {
    'target': {
        'address': 'Venice',
        'components': {
            'country': 'US',
            'administrative_area': 'California'
        }
    }
} %}
```

:::tip New Documentation
See the complete new [Region Biasing](/guides/region-biasing/) documentation.
:::
