# Visitor Geolocation

If you want to know where your visitors are coming from, you can perform a geolocation lookup of their IP address.

```twig
{% set visitor = googleMaps.getVisitor() %}
```

The `visitor` value contains the geolocation detection results in a [Geolocation Model](/models/geolocation-model/).

::: warning
These techniques rely on deducing a location from the user's IP address. Please be aware, this will rarely be 100% accurate. Generally speaking, you will end up with geolocation results which are within a few miles of the visitor's actual location, although occasionally they will be detected as much farther away.

A more precise method of visitor geolocation can be done using the HTML 5 geolocation feature. However, this will prompt the user to give your site permission to know their location, and it's possible (and common) for them to decline.
:::
