---
description:
---

# 🔧 Sorting entries by closest locations

<update-message/>

The general syntax for conducting a proximity search did not change:

```twig
{# BOTH OLD & NEW - EXACT SAME SYNTAX! #}
{% set entries = craft.entries.myAddressField(options).orderBy('distance').all() %}
```

However, while the syntax remains unchanged, the `options` parameter has been altered slightly. For more information, read about the updated [proximity search options](/proximity-search/options/).

:::tip New Documentation
See the complete new [Proximity Search](/proximity-search/) documentation.
:::
