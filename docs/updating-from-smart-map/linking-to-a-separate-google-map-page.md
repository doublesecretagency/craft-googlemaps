# 🔧 Linking to a separate Google Map page

<update-message/>

The functionality has remained nearly identical. However, the method names have been changed.

```twig
{# OLD #}
<a href="{{ address.googleMapUrl() }}">Open in Google Maps</a>

{# NEW #}
<a href="{{ address.linkToMap() }}">Open in Google Maps</a>
```

```twig
{# OLD #}
<a href="{{ address.directionsUrl() }}">Get Directions</a>

{# NEW #}
<a href="{{ address.linkToDirections() }}">Get Directions</a>
```

We also have two new methods for your convenience...

```twig
<a href="{{ address.linkToArea() }}">See area in Google Maps</a>

<a href="{{ address.linkToStreetView() }}">Open in Street View</a>
```

:::tip New Documentation
See the complete new [Linking to a Map](/guides/linking-to-a-map/) documentation.
:::
