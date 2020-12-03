# Visitor Geolocation

## getVisitor(config = [])

Perform geolocation of each site visitor. Automatically detects each user's IP address (unless manually specified).

:::code
```twig
{# Get visitor geolocation information #}
{% set visitor = googleMaps.getVisitor() %}

{# The exact same method, with optional overrides #}
{% set visitor = googleMaps.getVisitor({
    'service': 'ipstack',
    'ip': '1.2.3.4'
}) %}
```
```php
// Get visitor geolocation information
$visitor = GoogleMaps::getVisitor();

// The exact same method, with optional overrides
$visitor = GoogleMaps::getVisitor([
    'service' => 'ipstack',
    'ip' => '1.2.3.4'
]);
```
:::

You can also access this value as a magic property in Twig:

:::code
```twig
{% set visitor = googleMaps.visitor %}
```
:::

This is functionally identical to calling `getVisitor()` with no parameters.

---
---

:::warning More Info
For more information, check out the documentation on [Geolocation](/geolocation/).
:::
