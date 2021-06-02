---
description:
---

# How to Use

You'll want to use the simplest approach for getting visitor geolocation data nearly 100% of the time. However, there may be some extremely rare edge cases where you need to override the `service` and/or `ip` values at runtime.

## Basic

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
The vast majority of the time, the above example is all you will need. Assuming you don't need anything more complex from your visitor geolocation call, just stick with the example above.
:::

## Advanced

The `getVisitor` method is simple, yet flexible. You can override the `ip` or `service` values at runtime.

### `getVisitor($config = [])`

#### Arguments

- `$config` (_array_) - Optional config settings to override the following:

| Option    | Type     | Default               | Description                       |
|-----------|:--------:|:---------------------:|-----------------------------------|
| `ip`      | _string_ | (autodetects)         | The visitor's IP address.         |
| `service` | _string_ | (uses [setting](/getting-started/settings/#geolocation-service-dropdown-options)) | Which geolocation service to use. |

#### Returns

_Visitor_ - A [Visitor Model](/models/visitor-model/) representing the approximate location of a visitor.

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

:::tip Supported Geolocation Services
At this time, the only accepted values are `ipstack`, `maxmind`, or _false_. Additional third-party geolocation services may be added in the future.
:::

:::warning Low Precision Guaranteed
Regardless of which third-party service you are using, **geolocation is an imprecise science**.

A geolocation result will very often point to a user's _internet service provider_, or other internet routing equipment. While that location may be in the general region of the user, do not expect a geolocation result to point directly at a user's real location.
:::
