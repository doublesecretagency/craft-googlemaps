# `GoogleMaps` Helper Class

Whether you are working in Twig or PHP, the `GoogleMaps` helper class exists to make your life a little easier. It includes wrapper methods for some of the most common use-cases you will encounter.

### When using it in Twig

As long as this plugin is installed and enabled, the `googleMaps` object will be globally available in Twig.

### When using it in PHP

You must be sure to `use` the helper class in order to reference it in your own plugin or module.

```php
use doublesecretagency\googlemaps\helpers\GoogleMaps;
```

Throughout these docs, you will see references to `GoogleMaps` in PHP. Although it's rarely stated, in each instance you _must_ make sure to `use` the Helper class.

:::warning When should I use the helper methods?
**As much as possible.** If there is a helper method which directly addresses the problem that you are trying to solve, it is preferable to use the helper method rather than the underlying service method (or whatever the helper method may be wrapping). The goal of this is to provide consistency and uniformity across and within projects.

However, if your use case is _not_ covered by this collection of helper methods, then feel free to rely directly on the underlying service methods. It's unrealistic to expect the helper methods to cover 100% of use-cases, so do what you must to get the job done.
:::

---

## Generate Maps

```twig
{# Generate a dynamic map #}
{{ googleMaps.dynamic(locations, options) }}

{# Generate a static map #}
{{ googleMaps.static(locations, options) }}
```

For more information, check out the documentation on [Dynamic Maps](/maps/dynamic/) and [Static Maps](/maps/static/).

---

## Change a Marker Icon

```twig
{# Set the icon of a specific marker #}
{% do googleMaps.setMarkerIcon(mapId, markerId, icon) %}
```

For more information, check out the documentation on [Setting Marker Icons](/guides/setting-marker-icons/#change-a-marker-icon).

---

## Apply a KML file

```twig
{# Load a KML file into the specified map #}
{% do googleMaps.loadKml(mapId, kml, options) %}
```

For more information, check out the documentation on [KML Files](/guides/kml-files/).

---

## Perform Visitor Geolocation

```twig
{# Get visitor geolocation information #}
{% set visitor = googleMaps.visitor %}
```

You can optionally override the `service` and/or `ip` when performing a visitor geolocation.

```twig
{# It's the same method, but using optional overrides #}
{% set visitor = googleMaps.getVisitor({
    'service': 'ipstack',
    'ip': '1.2.3.4'
}) %}
```

For more information, check out the documentation on [Geolocation](/geolocation/).

---

## Geocoding (Address Lookups)

```twig
{# Get all matching results (sorted by best match) #}
{% set allMatches = googleMaps.lookup(target).all() %}

{# Get the first (best) matching result #}
{% set bestMatch = googleMaps.lookup(target).one() %}

{# Get only the coordinates of the first matching address #}
{% set coords = googleMaps.lookup(target).coords() %}
```

For more information, check out the documentation on [Geocoding](/geocoding/).

---

## Override Google API keys

If you need to override the Google API keys via Twig, you can...

```twig
{# Override server key #}
{% do googleMaps.setServerKey('lorem') %}

{# Override browser key #}
{% do googleMaps.setBrowserKey('ipsum') %}
```

There are parallel methods for retrieving the API keys...

```twig
{# Get server key #}
{% set serverKey = googleMaps.getServerKey() %}

{# Get browser key #}
{% set browserKey = googleMaps.getBrowserKey() %}
```

And since Twig is very forgiving about using magic methods, you can abbreviate those even further...

```twig
{{ googleMaps.serverKey }}
{{ googleMaps.browserKey }}
```
