---
description: In a typical proximity search, only the radius of the searcher is relevant. In a reverse proximity search, each location can have its own valid radius to account for.
meta:
- property: og:type
  content: website
- property: og:url
  content: https://plugins.doublesecretagency.com/google-maps/guides/reverse-proximity-search/
- property: og:title
  content: Reverse Proximity Search | Google Maps plugin for Craft CMS
- property: og:description
  content: In a typical proximity search, only the radius of the searcher is relevant. In a reverse proximity search, each location can have its own valid radius to account for.
- property: og:image
  content: https://plugins.doublesecretagency.com/google-maps/images/guides/proximity-search-reverse.png
- property: twitter:card
  content: summary_large_image
- property: twitter:url
  content: https://plugins.doublesecretagency.com/google-maps/guides/reverse-proximity-search/
- property: twitter:title
  content: Reverse Proximity Search | Google Maps plugin for Craft CMS
- property: twitter:description
  content: In a typical proximity search, only the radius of the searcher is relevant. In a reverse proximity search, each location can have its own valid radius to account for.
- property: twitter:image
  content: https://plugins.doublesecretagency.com/google-maps/images/guides/proximity-search-reverse.png
---

# Reverse Proximity Search

Typically, a proximity search is conducted by measuring the distance from a center point. You can optionally specify a search radius to limit results to be within a specific geographic circle.

In a typical [proximity search](/proximity-search/), this is specified via the standard [`range` option](/proximity-search/options/)...

:::code
```twig
{# Get results within 20 kilometers #}
{% set options = {
    'target': target,
    'range': 20,
    'units': 'km'
} %}
```
```php
// Get results within 20 kilometers
$options = [
    'target' => $target,
    'range' => 20,
    'units' => 'km'
];
```
:::

This approach works great for 98% of proximity search situations. It assumes that the **center point** is solely responsible for determining how far away a valid destination can be.

#### Visualization of a normal proximity search:

<img class="dropshadow" :src="$withBase('/images/guides/proximity-search-normal.png')" alt="Visualization of a normal proximity search" width="604" style="margin-top:10px; margin-bottom:10px;">

However, you will occasionally need to flip that logic around. Sometimes, each location will have a **different permissible range**. If each location has a different allowable radius, then you can't just rely on a singular search range from the point of origin.

## A Practical Example

Let's say you are running a network of radio stations. Each station should be available to users within a predetermined distance from the radio station. If you are close enough to a given station, you will be able to access their feed.

**But each station has a different antennae range!** Some stations are available within 5 kilometers of a user, others are available within 10 kilometers, etc. Your proximity search will need to take into account the individual radius of each location.

#### Visualization of a reverse proximity search:

<img class="dropshadow" :src="$withBase('/images/guides/proximity-search-reverse.png')" alt="Visualization of a reverse proximity search" width="604" style="margin-top:10px; margin-bottom:6px;">

In the example above, **there is only one valid result**. The user (green marker) is only within range (blue circle) of a single radio station.

## Reverse Radius

When you need to perform a reverse proximity search, there are only two simple steps:

1. Add a new **Number** field to your existing locations channel. Name it "Location Range" (or whatever you prefer). This new field will be used to store the radius of each location.

2. When writing the proximity search query, use the [`reverseRadius` option](/proximity-search/options/). Specify the handle of your new field (ie: `locationRange`).

By specifying a `reverseRadius` value, the plugin will flip the proximity search around, using the radius of each individual location to determine whether to include it in the search results.

:::code
```twig
{# Use the `locationRange` field for a reverse proximity search #}
{% set options = {
    'target': target,
    'reverseRadius': 'locationRange'
} %}
```
```php
// Use the `locationRange` field for a reverse proximity search
$options = [
    'target' => $target,
    'reverseRadius' => 'locationRange'
];
```
:::

For context, here is a complete example using the [`reverseRadius` option](/proximity-search/options/)...

:::code
```twig
{# Get proximity search target from user query #}
{% set target = craft.app.request.getParam('near') ?? null %}

{# Configure the reverse proximity search #}
{% set options = {
    'target': target,
    'reverseRadius': 'locationRange'
} %}

{# Run the reverse proximity search #}
{% set entries = craft.entries
    .section('locations')
    .myAddressField(options)
    .orderBy('distance')
    .all() %}
```
```php
// Get proximity search target from user query
$target = Craft::$app->request->getParam('near') ?? null;

// Configure the reverse proximity search
$options = [
    'target' => $target,
    'reverseRadius' => 'locationRange'
];

// Run the reverse proximity search
$entries = Entry::find()
    ->section('locations')
    ->myAddressField($options)
    ->orderBy('distance')
    ->all();
```
:::

With the `reverseRadius` in place, your search results will include nearby locations according to the **range of each individual location**.
