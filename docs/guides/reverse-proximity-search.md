---
description:
---

# Reverse Proximity Search

Typically, a proximity search is conducted by measuring the **distance from a center point**. You can optionally specify a search radius to limit results to be within a specific geographic circle.

In a [typical proximity search](/proximity-search/), this is specified via the standard [`range` option](/proximity-search/options/)...

:::code
```twig
{# Get results within 50 miles #}
{% set options = {
    'target': target,
    'range': 50
} %}
```
```php
// Get results within 50 miles
$options = [
    'target' => $target,
    'range' => 50
];
```
:::

[PICTURE OF A TYPICAL SEARCH RADIUS]

This approach works great for 95% of situations. It assumes that the **center point** determines how far away a location is allowed to be.

Occasionally, however, you will need to flip that logic around. If each location has a distinct allowable radius, then you can't rely on a singular search range from the point of origin.

## A Practical Example

Let's say you are running a network of radio stations. Each station should be available to users within a predetermined distance from the radio station. If you are close enough to a given station, you will be able to access their feed.

But **each station has a different radius**. Some stations are available within 5 miles of a user, others are available within 10 miles, etc.

In other words, your proximity search needs to look more like this...

[PICTURE OF A REVERSE PROXIMITY SEARCH, MULTIPLE RADII]

## Reverse Radius

When you need to perform a reverse proximity search, there are only two simple steps:

1. Add a new `Number` field to your existing locations channel. You can name it "Location Range" (or whatever you'd prefer). This new field will be used to store the radius of each location.

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
