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
    .styles(stylesArray)
    .kml(url);
```
```twig
{# Very basic #}
{% set map = googleMaps.map(locations, options) %}

{# More complex #}
{% set map = googleMaps.map()
    .markers(locations, options)
    .styles(stylesArray)
    .kml(url) %}
```
```php
// Very basic
$map = GoogleMaps::map($locations, $options);

// More complex
$map = GoogleMaps::map()
    ->markers($locations, $options)
    ->styles($stylesArray)
    ->kml($url);
```
:::

In the examples above, you can see that we are _chaining_ methods together in order to build a map. There are several methods in the API, which can be chained in any order necessary.
 
A chain must always **begin** with the creation of a `map` object.
