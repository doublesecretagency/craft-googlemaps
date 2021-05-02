---
description:
---

# 🔧 Visitor Geolocation

<update-message/>

The syntax of a visitor geolocation has changed:

:::code
```twig
{# OLD #}
{% set visitor = craft.smartMap.visitor %}

{# NEW #}
{% set visitor = googleMaps.getVisitor() %}
```
```php
// OLD
$visitor = SmartMap::$plugin->smartMap->visitor;

// NEW
$visitor = GoogleMaps::getVisitor();
```
:::

Additionally, the method will now return a complex model (instead of a simple array of data). The [Visitor Model](/models/visitor-model/) extends the [Location Model](/models/location-model/), so both sets of properties and methods are available.

:::tip New Documentation
See the complete new [Visitor Geolocation](/geolocation/) documentation.
:::
