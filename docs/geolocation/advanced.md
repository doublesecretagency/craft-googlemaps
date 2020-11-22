# Advanced Geolocation

Virtually 100% of the time, you'll want to use the simplest approach for getting visitor geolocation data. However, there may be some extremely rare edge cases where you need to override the `service` and/or `ip` values at runtime.

## Simple

:::code
```twig
{% set visitor = googleMaps.getVisitor() %}  {# As a normal method #}
{% set visitor = googleMaps.visitor %}       {# As a magic property #}
```
```php
$visitor = GoogleMaps::getVisitor();
// No magic property equivalent 
```
:::

By default, the `getVisitor` method will:
 - Automatically detect the visitor's IP address.
 - Automatically use the lookup service specified by the [plugin settings](/getting-started/settings/).

:::warning Perfect Example
99% of the time the above example is all you need. Assuming you don't need anything more complex from your visitor geolocation call, just stick with the example above.
:::

## Advanced

:::code
```twig
{% set visitor = googleMaps.getVisitor({
    'service': 'maxmind',
    'ip': '1.2.3.4',
}) %}
```
```php
$visitor = GoogleMaps::getVisitor([
    'service' => 'maxmind',
    'ip' => '1.2.3.4',
]);
```
:::

This allows you to manually specify the `service` and/or `ip`.

::: tip SUPPORTED GEOLOCATION SERVICES
At this time, the only accepted values are `ipstack`, `maxmind`, or _false_. Additional third-party geolocation services may be added in the future.
:::

You can take a look at the underlying [Geolocation Service](/services/geolocation-service/#getvisitor) to learn more about how the `getVisitor` method works. But remember, it's always preferable to use the [helper method](/helper/#perform-visitor-geolocation), instead of relying on the service method directly.
