# Dynamic Map Model

The Dynamic Map Model is critical for generating a [Dynamic Map](/maps/dynamic/). You can chain (almost) any of the methods together in any order you'd like.

## Example Usage

### Simple:

```twig
{# Get all locations #}
{% set locations = craft.entries.section('locations').all() %}

{# Display them on a map #}
{{ googleMaps.map(locations).html() }}
```

### Complex:

```twig
{# Build a map with two sets of locations and a KML file #}
{% set map = googleMaps.map(locations, mapOptions)
    .markers(moreLocations, markerOptions)
    .kml(filename)
%}

{# Render map #}
{{ map.html() }}
```

:::warning The Magic of Chaining
With only a few exceptions, almost all of the methods listed below can be chained in any order you'd like. Chaining can be a powerful technique, allowing you to build complex maps with ease.

 To get a better understanding of how it works, read more on [Chaining Methods](/maps/chaining/).
:::

## Public Methods

### `__construct($locations = [], $options = [])`

This method will be called when you initialize a Dynamic Map object. It gives you a starting point to begin customizing the map in whatever way you see fit. Once you have the map object, you can then chain other methods to customize the map. 

This is the method that sets the chain in motion. It must be called _first_ in order to initialize the Dynamic Map object. Once you have initialized a map, you will be able to build upon it by adding markers, KML layers, etc.

#### Arguments

 - `$locations` (_mixed_) - See a description of acceptable [locations...](/maps/locations/)
 - `$options` (_array_) - Optional parameters to configure the map.

#### Returns

_self_ - This instance of the Dynamic Map Model. By returning a static self reference, chaining is possible.

:::code
```twig
{% set map = googleMaps.map(locations) %}
```
```php
$map = GoogleMaps::map($locations);
```
:::

:::warning map
In each of the remaining examples, `map` will be an instance of the **Dynamic Map Model**.
:::

---
---

### `markers($locations, $options = [])`

Append markers to an existing map object.

#### Arguments

 - `$locations` (_mixed_) - See a description of acceptable [locations...](/maps/locations/)
 - `$options` (_array_) - Optional parameters to configure the markers.

#### Returns

_self_ - This instance of the Dynamic Map Model. By returning a static self reference, chaining is possible.

:::code
```twig
{% do map.markers(locations) %}
```
```php
$map->markers($locations);
```
:::

---
---

### `kml($files, $options = [])`

Append one or more KML layers to an existing map object.

#### Arguments

 - `$files` (_mixed_)
 - `$options` (_array_) - Optional parameters to configure the KML layers.

#### Returns

_self_ - This instance of the Dynamic Map Model. By returning a static self reference, chaining is possible.

:::code
```twig
{% do map.kml(files) %}
```
```php
$map->kml($files);
```
:::

---
---

### `styles($stylesArray)`

Style a map based on a given array of styles.

:::tip Generating Styles
There are many ways to generate an array of map styles. The most popular approach is to use a service like one of the following:

 - example 1
 - example 2
:::

#### Arguments

 - `$stylesArray` (_array_) - A set of styles to be applied to the map.

#### Returns

_self_ - This instance of the Dynamic Map Model. By returning a static self reference, chaining is possible.

:::code
```twig
{% do map.styles(stylesArray) %}
```
```php
$map->styles($stylesArray);
```
:::

---
---

### `html($init = true)`

Render the necessary `<div>` container to hold the map.

#### Arguments

 - `$init` (_bool_) - Whether to automatically initialize the map DNA via JavaScript. If this is set to `false`, the map DNA will need to be manually initialized at some point in the future.

#### Returns

_Markup_ - A Twig Markup instance, ready to be rendered via curly braces (`{{ }}`).

:::code
```twig
{{ map.html() }}
```
```php
$twigMarkup = $map->html();
```
:::
