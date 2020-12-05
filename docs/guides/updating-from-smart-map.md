# Updating from Smart Map

Each subject below refers to a page from the original Smart Map documentation...

 - [Using an Address field](#using-an-address-field)
 - [Render a map in Twig](#render-a-map-in-twig)
 - [Sorting entries by closest locations](#sorting-entries-by-closest-locations)
 - [Filtering entries by subfield value](#filtering-entries-by-subfield-value)
 - [Filtering out entries with invalid coordinates](#filtering-out-entries-with-invalid-coordinates)
 - [Using a filter fallback in proximity searches](#using-a-filter-fallback-in-proximity-searches)
 - [Region Biasing](#region-biasing)
 - [Internationalization Support](#internationalization-support)
 - [Customizing the map in Twig](#customizing-the-map-in-twig)
 - [Manipulating the map in JavaScript](#manipulating-the-map-in-javascript)
 - [Styling a Map](#styling-a-map)
 - [KML files](#kml-files)
 - [Adding marker info bubbles](#adding-marker-info-bubbles)
 - [Different icons for different marker types](#different-icons-for-different-marker-types)
 - [Linking to a separate Google Map page](#linking-to-a-separate-google-map-page)
 - [How to use with a Matrix field](#how-to-use-with-a-matrix-field)
 - [Automatically format an entire address](#automatically-format-an-entire-address)
 - ["isEmpty" and "hasCoords"](#isempty-and-hascoords)
 - [Front-End Address Lookup](#front-end-address-lookup)
 - [Front-End Entry Form](#front-end-entry-form)
 - [Importing Addresses](#importing-addresses)
 - [Exporting the Address data](#exporting-the-address-data)
 - [Get Google API keys](#get-google-api-keys)
 - [Override Google API keys](#override-google-api-keys)
 - [Visitor Geolocation](#visitor-geolocation)
 - [Map Debug Page](#map-debug-page)
 - [Troubleshooting](#troubleshooting)

---
---

## Using an Address field

An Address field now provides you with a complete [Address Model](/models/address-model/). To get an idea of what is possible, read about [using an Address in Twig](/address-field/in-twig/).

:::tip Complete Documentation
See the new [Address Field](/address-field/) documentation.
:::

## Render a map in Twig

### Dynamic maps

```twig
{# OLD #}
{{ craft.smartMap.map(locations, options) }}

{# NEW #}
{{ googleMaps.map(locations, options).tag() }}
```

Some of the `options` have changed. Take a look at the new available [dynamic map options](/dynamic-maps/map-management/#map-locations-options).

:::tip Complete Documentation
See the new [Dynamic Maps](/dynamic-maps/) documentation.
:::

### Static maps

Render a static map `<img>` tag.

```twig
{# OLD #}
{{ craft.smartMap.img(locations, options) }}

{# NEW #}
{{ googleMaps.img(locations, options).tag() }}
```

Render a static map `src` attribute value.

```twig
{# OLD #}
{{ craft.smartMap.imgSrc(locations, options) }}

{# NEW #}
{{ googleMaps.img(locations, options).src() }}
```

:::tip Complete Documentation
See the new [Static Maps](/static-maps/) documentation.
:::

## Sorting entries by closest locations

The general syntax for conducting a proximity search did not change: 

```twig
{# BOTH OLD & NEW - EXACT SAME SYNTAX! #}
{% set entries = craft.entries.myAddressField(options).orderBy('distance').all() %}
```

But while the syntax remains unchanged, the `options` have changed slightly. For more information, read about the available [proximity search options](/proximity-search/options/).

:::tip Complete Documentation
See the new [Proximity Search](/proximity-search/) documentation.
:::




## Filtering entries by subfield value
## Filtering out entries with invalid coordinates
## Using a filter fallback in proximity searches
## Region Biasing
## Internationalization Support
## Customizing the map in Twig
## Manipulating the map in JavaScript
## Styling a Map
## KML files
## Adding marker info bubbles
## Different icons for different marker types
## Linking to a separate Google Map page
## How to use with a Matrix field
## Automatically format an entire address
## "isEmpty" and "hasCoords"
## Front-End Address Lookup
## Front-End Entry Form
## Importing Addresses
## Exporting the Address data
## Get Google API keys
## Override Google API keys
## Visitor Geolocation
## Map Debug Page
## Troubleshooting
