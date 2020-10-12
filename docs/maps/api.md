# API

## Universal API

In an effort to smooth the development process, you can effectively call the exact same methods across various languages. Whether you are working in JavaScript, Twig, or PHP, the commands to create a map are all nearly identical.

## Basic Examples

Switch between languages to see the similarities...

:::code
```js
// Very basic
var map = googleMaps.map(locations, options);

// More complex
var map = googleMaps.map()
    .markers(locations, options)
    .styles(styleSet)
    .kml(url);
```
```twig
{# Very basic #}
{% set map = googleMaps.map(locations, options) %}

{# More complex #}
{% set map = googleMaps.map()
    .markers(locations, options)
    .styles(styleSet)
    .kml(url) %}
```
```php
// Very basic
$map = GoogleMaps::map($locations, $options);

// More complex
$map = GoogleMaps::map()
    ->markers($locations, $options)
    ->styles($styleSet)
    ->kml($url);
```
:::

In the examples above, you can see that we are _chaining_ methods together in order to build a map. There are several methods in the API, which can be chained in any order necessary.

For complete information, see the page regarding [Chaining...](/maps/chaining/)




### Simple:

```twig
{# Get all locations #}
{% set locations = craft.entries.section('locations').all() %}

{# Display them on a map #}
{{ googleMaps.map(locations).tag() }}
```

### Complex:

```twig
{# Build a map with two sets of locations and a KML file #}
{% set map = googleMaps.map(locations, mapOptions)
    .markers(moreLocations, markerOptions)
    .kml(filename)
%}

{# Render map #}
{{ map.tag() }}
```




## Languages

If you are working in JavaScript, please refer to the [Universal Methods](/maps/universal-methods/) and [JavaScript Methods](/maps/javascript-methods/).

If you are working in Twig or PHP, please refer to the [Universal Methods](/maps/universal-methods/) and [Twig & PHP Methods](/maps/twig-php-methods/).
