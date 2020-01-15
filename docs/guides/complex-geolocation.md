# Complex Geolocation

Virtually 100% of the time, you'll want to use the simplest approach for getting visitor geolocation data. However, there may be some extremely rare edge cases where you need to override the `service` and/or `ip` values at runtime.

## Simple

```twig
{% set visitor = googleMaps.visitor %}
```

In its simplest form, the geolocation call uses the geolocation service specified in the `geolocationService` config setting. It also detects the IP address of each visitor automatically.

## Complex

```twig
{% set visitor = googleMaps.getVisitor({
    'service': 'maxmind',
    'ip': '1.2.3.4',
}) %}
```

You can optionally override either the `service` and/or the `ip`.

::: tip SUPPORTED GEOLOCATION SERVICES
At this time, the only accepted values are `"ipstack"`, `"maxmind"`, or _false_. Additional third-party geolocation services may be added in the future.
:::

To see more about how the `getVisitor` method works, take a look at the [Geolocation Service...](/services/geolocation-service/#getvisitor)
