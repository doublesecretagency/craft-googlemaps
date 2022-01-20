---
description: Use the visitor's IP address to automatically determine the starting point of a proximity search.
---

# Search by Visitor IP

It is remarkably simple to grab the IP address of a visitor, perform a [visitor geolocation](/geolocation/) lookup, and then use that information to perform a normal [proximity search](/proximity-search/).

### Visitor Geolocation

Before you can perform any visitor geolocation, you must have a [geolocation service](/geolocation/service-providers/) account.

With that in place, it's quite easy to get the visitor geolocation information.

:::code
```twig
{% set visitor = googleMaps.visitor %}
```
```php
$visitor = GoogleMaps::getVisitor();
```
:::

### Proximity Search

From there, it's simply a matter of using the `visitor` coordinates in your proximity search.

:::code
```twig
{# Get visitor info #}
{% set visitor = googleMaps.visitor %}

{# Proximity search based on visitor coordinates #}
{% set options = {
    'target': visitor.coords
} %}
```
```php
// Get visitor info
$visitor = GoogleMaps::getVisitor();

// Proximity search based on visitor coordinates
$options = [
    'target' => $visitor->coords
];
```
:::

:::warning Bypasses Google API
Conveniently, the Google API will not be pinged under these circumstances. When you use a set of [coordinates](/models/coordinates/) as the proximity search [target](/proximity-search/options/#target), no additional API call is necessary.
:::
