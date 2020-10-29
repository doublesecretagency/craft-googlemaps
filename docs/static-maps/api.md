# API

## Static API

Whether you are working in Twig or PHP, the commands to create a map are all nearly identical.

:::tip List of Methods
To see everything that's possible with a static map, take a look at the [`StaticMap` model](/models/static-map-model/).
:::

## Practical Examples

Switch between languages to see the similarities...

### Basic Map

:::code
```twig
{% set map = googleMaps.map(locations, options) %}
```
```php
$map = GoogleMaps::map($locations, $options);
```
:::

### Complex Map

:::code
```twig
{% set map = googleMaps.map()
    .markers(locations, options)
    .path(locations)
    .styles(styleSet)
    .center(coords)
    .zoom(level) %}
```
```php
$map = GoogleMaps::map()
    ->markers($locations, $options)
    ->path($locations)
    ->styles($styleSet)
    ->center($coords)
    ->zoom($level);
```
:::

You'll notice that we are _chaining_ methods together in order to build a map. There are several methods in the API, which can be chained in any order necessary.

To get a better understanding of how it works, read more on [Chaining](/static-maps/chaining/).
