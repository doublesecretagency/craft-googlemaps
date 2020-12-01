# Visitor Geolocation

getVisitor(array $config = [])


---
---




## Perform Visitor Geolocation

The `visitor` attribute relies on the magic getter of `getVisitor`. They are functionally identical.

Optionally, you can override the `service` and/or `ip` when performing a visitor geolocation.

:::code
```twig
{# Get visitor geolocation information #}
{% set visitor = googleMaps.visitor %}

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

For more information, check out the documentation on [Geolocation](/geolocation/).
