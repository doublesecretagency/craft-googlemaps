# 🔧 Override Google API keys

<update-message/>

The syntax for overriding API keys has barely changed...

```twig
{# OLD #}
{% do craft.smartMap.setBrowserKey('lorem') %}
{% do craft.smartMap.setServerKey('ipsum') %}

{# NEW #}
{% do googleMaps.setBrowserKey('lorem') %}
{% do googleMaps.setServerKey('ipsum') %}
```

Effectively, you only need to replace `craft.smartMap` with `googleMaps`.

:::tip New Documentation
See the complete new [internal API](/helper/api/#setbrowserkey-key) documentation.
:::
