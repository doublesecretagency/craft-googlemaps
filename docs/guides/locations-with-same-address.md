---
description: When multiple locations share the same address (like two doctors in the same medical building), there can be some confusion about how to display them on a map.
meta:
  - property: og:type
    content: website
  - property: og:url
    content: https://plugins.doublesecretagency.com/google-maps/guides/locations-with-same-address/
  - property: og:title
    content: Locations with the Same Address | Google Maps plugin for Craft CMS
  - property: og:description
    content: When multiple locations share the same address (like two doctors in the same medical building), there can be some confusion about how to display them on a map.
  - property: og:image
    content: https://plugins.doublesecretagency.com/google-maps/images/guides/same-address.png
  - property: twitter:card
    content: summary_large_image
  - property: twitter:url
    content: https://plugins.doublesecretagency.com/google-maps/guides/locations-with-same-address/
  - property: twitter:title
    content: Locations with the Same Address | Google Maps plugin for Craft CMS
  - property: twitter:description
    content: When multiple locations share the same address (like two doctors in the same medical building), there can be some confusion about how to display them on a map.
  - property: twitter:image
    content: https://plugins.doublesecretagency.com/google-maps/images/guides/same-address.png
---

# Locations with the Same Address

When multiple locations share the same address (ie: two doctors in the same medical building), there can be some confusion about how to display them on a map. If you were to draw separate markers for each address, one marker would be covering the other.

Admittedly, it's a tricky problem to solve. There may be not be a perfect solution, so here is an imperfect one...

## 1. Use an info window

If you're worried about overlapping markers, you may already be using [info windows](/dynamic-maps/info-windows/) on your map. It's likely that the info window was a clue you had a problem with overlapping markers in the first place.

Use the info window to display all relevant information about a given address. You can use the address to determine which other businesses share the same coordinates.

## 2. List all matching locations

Within the context of the info window, **find and list all matching locations**. This means that each info window may show more than one location (if they share the same coordinates).

To get all locations with matching coordinates, the example below uses the [`subfields` option](/proximity-search/options/#subfields).

## Example

Here's a sample info window, listing multiple businesses with the same address...

<img class="dropshadow" :src="$withBase('/images/guides/same-address.png')" alt="Example of an info window listing multiple businesses with the same address">

:::code 
```twig my-info-window.twig
{# Filter by subfields matching existing coordinates #}
{% set options = {
    'subfields': {
        'lat': entry.myAddressField.lat,
        'lng': entry.myAddressField.lng
    }
} %}

{# Get all entries with these exact coordinates #}
{% set addressMatches = craft.entries.myAddressField(options).all() %}

{# Display the common shared address #}
<div>{{ entry.myAddressField.multiline }}</div>

{# Loop through entries at this address #}
{% for addressEntry in addressMatches %} {# don't override `entry`! #}
    <div>
        <div>{{ addressEntry.title }}</div>
        <div><a href="{{ addressEntry.url }}">More Info</a></div>
    </div>
{% endfor %}
```
:::
