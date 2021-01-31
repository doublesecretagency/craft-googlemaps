# 🔧 `isEmpty` and `hasCoords`

<update-message/>

No changes are necessary, both of these methods are identical to their previous iterations:

```twig
{# BOTH OLD & NEW - EXACT SAME SYNTAX! #}
{% if not address.isEmpty() %}
    {{ address }}
{% endif %}
```

:::tip New Documentation
See the complete [isEmpty](/models/address-model/#isempty) documentation.
:::

---
---

```twig
{# BOTH OLD & NEW - EXACT SAME SYNTAX! #}
{% if address.hasCoords() %}
    Latitude:  {{ address.lat }}
    Longitude: {{ address.lng }}
{% endif %}
```

:::tip New Documentation
See the complete [hasCoords](/models/location-model/#hascoords) documentation.
:::
