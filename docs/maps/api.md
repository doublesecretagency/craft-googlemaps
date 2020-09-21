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

## Starting a Chain
 
A chain must always **begin** with the creation of a `map` object. No matter how you intend to decorate your dynamic map, it will always start the same way...

:::code
```js
var map = googleMaps.map(locations, options);
```
```twig
{% set map = googleMaps.map(locations, options) %}
```
```php
$map = GoogleMaps::map($locations, $options);
```
:::

:::warning Two flavors of "map object"
Internally, there are really two different things that are being referred to as the "map object". When working with Twig or PHP, it will be a [Dynamic Map Model](/models/dynamic-map-model/). But in JavaScript, the map object is actually a self-reference to the [`googleMaps` model](/models/javascript/).

 The structure of this object varies greatly between JS and PHP/Twig, but the API has been designed to make usage nearly identical regardless of language.
:::

## Ending a Chain

Not all chains need to be concluded. You may sometimes find it helpful to keep a chain alive long enough to perform more operations on the map object. Eventually though, you'll probably want to end the chain.

To end the chain, apply the `tag` method to wrap it all up. Depending on which language you are working in, you'll notice properties unique to that language.

:::code
```js
// Creates a new element to be placed in the DOM
var mapDiv = map.tag();
```
```twig
{# Renders a map in the Twig template #}
{{ map.tag() }}
```
```php
// Creates a new Twig\Markup object
$twigMarkup = $map->tag();
```
:::

The `tag` method will output the complete map. But please note, **the output will be significantly different for each language.** Even though the `tag` method exists in each language, the purpose for using it varies greatly between languages.

:::warning What to Expect
In [JavaScript](/maps/javascript-methods/#tag), `tag` creates a new element, to be placed in the DOM as you wish.

In [Twig](/maps/twig-php-methods/#tag-autorender-true), `tag` renders a finished map.

In [PHP](/maps/twig-php-methods/#tag-autorender-true), `tag` creates a new `Twig\Markup` object.
:::

## Languages

If you are working in JavaScript, please refer to the [Universal Methods](/maps/universal-methods/) and [JavaScript Methods](/maps/javascript-methods/).

If you are working in Twig or PHP, please refer to the [Universal Methods](/maps/universal-methods/) and [Twig & PHP Methods](/maps/twig-php-methods/).
