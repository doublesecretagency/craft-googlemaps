# Chaining

## What & Why

The `map` method will generate a [Dynamic Map Model](/models/dynamic-map-model/). You can create a dynamic map containing a set of markers, then render the `<div>` container tag.

There are several methods on the Dynamic Map Model that can be chained together. When combining these methods, it will be possible for you to mix & match information in order to build the map in any way you please.


:::warning The Magic of Chaining
With only a couple of exceptions (`map` and `html`), virtually all methods listed below can be chained in any order you'd like. Chaining can be a powerful technique, allowing you to build complex maps with ease.

 To get a better understanding of how it works, read more on [Chaining Methods](/maps/chaining/).
:::


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
Internally, there are really two different things that are being referred to as the "map object".

 - In Twig/PHP, it's a [Dynamic Map Model](/models/dynamic-map-model/).
 - In JavaScript, it's a [`DynamicMap` model](/javascript/dynamicmap.js/).

 The internal structure of this object varies greatly between JS and PHP/Twig, but the API has been designed to make usage nearly identical regardless of language.
:::

## Ending a Chain

Not all chains need to be concluded right away... you may sometimes find it helpful to keep a chain alive long enough to perform more operations on the map object. Eventually though, you'll probably want to end the chain.

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

The `tag` method will create a `<div>` tag containing the finished map. But please note, **the output will be significantly different for each language.** Even though the `tag` method exists in each language, the purpose for using it varies greatly between languages.

:::warning What to Expect
In [JavaScript](/maps/javascript-methods/#tag), `tag` creates a new element, to be placed in the DOM as you wish.

In [Twig](/maps/twig-php-methods/#tag-autorender-true), `tag` renders a finished map.

In [PHP](/maps/twig-php-methods/#tag-autorender-true), `tag` creates a new `Twig\Markup` object.
:::
