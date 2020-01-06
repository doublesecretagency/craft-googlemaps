# Twig Object

As long as the plugin is installed and enabled, the `googleMaps` object will be predefined in Twig.

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
{# Do a visitor geolocation lookup #}
{% set visitor = googleMaps.getGeolocation() %}
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
