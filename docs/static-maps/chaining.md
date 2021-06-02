---
description: Just like dynamic maps, complex static maps can be created by chaining methods together. Learn how to customize a static map in both Twig and PHP.
---

# Chaining

## What & Why

**Chaining** refers to the ability to run multiple commands on a map sequentially...

:::code
```twig
{% set map = googleMaps.img(locations)
    .styles(styleSet)
    .center(coords)
    .zoom(level) %}
```
```php
$map = GoogleMaps::img($locations)
    ->styles($styleSet)
    ->center($coords)
    ->zoom($level);
```
:::

:::tip Static & Dynamic
All the examples on this page demonstrate how [Static Map methods](/models/static-map-model/) can be chained, but it's worth noting that [Dynamic Map methods](/models/dynamic-map-model/) can also be chained together.
:::

## Starting a Chain

A chain must always **begin** with the creation of a map object. No matter how you intend to decorate your static map, it will always start the same way...

:::code
```twig
{% set map = googleMaps.img(locations, options) %}
```
```php
$map = GoogleMaps::img($locations, $options);
```
:::

:::warning The "map object"
Internally, the "map object" refers to a [Static Map Model](/models/static-map-model/).
:::

## Ending a Chain

Not all chains need to be concluded right away... you may sometimes find it helpful to keep a chain alive long enough to perform more operations on the map object. Eventually though, you'll probably want to end the chain.

To end the chain, apply either the `tag` method or `src` method to wrap it all up.

### `tag`

Outputs a fully-rendered `<img>` tag for the Twig template.

:::code
```twig
{# Renders a map #}
{{ map.tag() }}
```
```php
// Creates a new Twig\Markup object
$twigMarkup = $map->tag();
```
:::

### `src`

Gets only the fully-configured URL of the static map.

:::code
```twig
{# Returns the static image URL #}
<img src="{{ map.src() }}">
```
```php
// Returns the static image URL
$imgSrc = $map->src();
```
:::
