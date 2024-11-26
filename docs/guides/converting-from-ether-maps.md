---
description: If migrating from the Ether Maps plugin, it's very simple to import all of your existing Address data into the Google Maps plugin.
meta:
  - property: og:type
    content: website
  - property: og:url
    content: https://plugins.doublesecretagency.com/google-maps/guides/converting-from-ether-maps/
  - property: og:title
    content: Converting from Ether Maps | Google Maps plugin for Craft CMS
  - property: og:description
    content: If migrating from the Ether Maps plugin, it's very simple to import all of your existing Address data into the Google Maps plugin.
  - property: og:image
    content: https://plugins.doublesecretagency.com/google-maps/images/guides/convert-from-ether-maps.png
  - property: twitter:card
    content: summary_large_image
  - property: twitter:url
    content: https://plugins.doublesecretagency.com/google-maps/guides/converting-from-ether-maps/
  - property: twitter:title
    content: Converting from Ether Maps | Google Maps plugin for Craft CMS
  - property: twitter:description
    content: If migrating from the Ether Maps plugin, it's very simple to import all of your existing Address data into the Google Maps plugin.
  - property: twitter:image
    content: https://plugins.doublesecretagency.com/google-maps/images/guides/convert-from-ether-maps.png
---

# Converting from Ether Maps

:::warning Minimum Craft Requirement
This feature requires a minimum of Craft **4.13.3**+ or **5.5.3**+.
:::

## Importing Address Data

Converting from an Ether Maps "Map" field to an "Address (Google Maps)" field is relatively straightforward... simply **switch the field type** on the field's settings page.

**Just change the field type!**

<img class="dropshadow" :src="$withBase('/images/guides/convert-from-ether-maps.png')" alt="Screenshot of field type select being switched to Address (Google Maps)" width="273" style="margin-left:20px; margin-bottom:4px;">

After saving the field as an "Address (Google Maps)" field, all data associated with that field will automatically be imported into the Google Maps plugin.

### One Field at a Time

When you update a single field, only the data for that specific field will be migrated over. Each Address field will need to be converted individually.

:::tip Field Configuration Not Included
The field's **settings** will not be ported, only the field's existing **data** will be transferred over.

You may still want to configure the Google Maps field to your liking, it will not automatically reflect how you had it configured with Ether Maps.
:::

### Deploying to Production

When deploying to a production environment, you'll most likely be using [Project Config](https://craftcms.com/docs/5.x/system/project-config.html) to keep all of your configuration settings in sync. Don't worry, there will be little (or no) action required on your part.

All relevant Address data will be converted **once your Project Config changes are applied**.

## Twig Template Changes

### Proximity Search

For proximity searches, you'll likely need to update your templates to reflect the new field type. Toggle between the Google Maps implementation and the Ether Maps implementation to see what's changed.

Pay close attention to the different [options](/proximity-search/options/) available for proximity searches.

:::code
```twig Google Maps (new)
{% set entries = craft.entries
    .myAddressField({
        'target': 'Barcelona, Spain',
        'range': 100,
        'units': 'mi'
    })
    .orderBy('distance')
    .all() %}
```
```twig Ether Maps (old)
{% set entries = craft.entries
    .myMapField({
        'location': 'Barcelona, Spain',
        'radius': 100,
        'unit': 'mi',
    })
    .orderBy('distance')
    .all() %}
```
:::

|                                                                     | Google Maps (new) | Ether Maps (old) |
|---------------------------------------------------------------------|-------------------|------------------|
| [Center of the proximity search](/proximity-search/options/#target) | `target`          | `location`       |
| [Range of the proximity search](/proximity-search/options/#range)   | `range`           | `radius`         |
| [Units of measurement for range](/proximity-search/options/#units)  | `units`           | `unit`           |

<img class="dropshadow" :src="$withBase('/images/proximity-search/search-terms.png')" alt="Diagram of common proximity search terms" style="max-width:100%; margin-top:4px;">

:::warning Complex Proximity Searches
For more complex searches, see the complete [Proximity Search](/proximity-search/) documentation.
:::

### Dynamic Maps

Ether Maps provides two ways to render a dynamic map:

```twig
{# Using the field directly #}
{{ entry.myMapField.embed(options) }}

{# Using the variable method #}
{{ craft.maps.embed(options) }}
```

Google Maps provides a [single method](/dynamic-maps/basic-map-management/#map-locations-options) for rendering a dynamic map:

```twig
{{ googleMaps.map(locations, options).tag() }}
```

Take a closer look at what counts as a [location](/dynamic-maps/locations/) and the available [options](/dynamic-maps/basic-map-management/#dynamic-map-options). They will not align 1:1 with the Ether Maps plugin, so you may need to adjust your templates accordingly.

:::warning More Info
For more information, see the complete [Dynamic Maps](/dynamic-maps/) documentation.
:::

### Static Maps

Ether Maps provides two ways to render a static map:

```twig
{# Using the field directly #}
{{ entry.myMapField.img(options) }}

{# Using the variable method #}
{{ craft.maps.img(options) }}
```

Google Maps provides a [single method](/models/static-map-model/#construct-locations-options) for rendering a static map:

```twig
{{ googleMaps.img(locations, options).tag() }}
```

Take a closer look at what counts as a [location](/dynamic-maps/locations/) and the available [options](/models/static-map-model/#static-map-options). They will not align 1:1 with the Ether Maps plugin, so you may need to adjust your templates accordingly.

:::warning More Info
For more information, see the complete [Static Maps](/static-maps/) documentation.
:::

## GraphQL

While both Ether Maps and Google Maps support GraphQL, their implementations are different. Please see the complete [GraphQL](/graphql/) documentation for more information.

:::warning No GraphQL support for Proximity Search
Although the Ether Maps plugin supports proximity searches via GraphQL, the Google Maps plugin currently does not.
:::

For expanded GraphQL support, see [this GitHub thread](https://github.com/doublesecretagency/craft-googlemaps/issues/73) and feel free to leave a comment.
