---
description: With few exceptions, all chainable methods are identical across JavaScript, Twig, and PHP. Easily switch between languages using the universal API.
---

# Universal API

In an effort to smooth the development process, you can effectively call the exact same methods across various programming languages. Whether you are working in JavaScript, Twig, or PHP, the commands to create a map are all nearly identical.

Take a look at the following examples to see how the same concepts translate across different languages.

## Practical Examples

Switch between languages to see the similarities...

### Basic Map

:::code
```js
const map = googleMaps.map(locations, options);
```
```twig
{% set map = googleMaps.map(locations, options) %}
```
```php
$map = GoogleMaps::map($locations, $options);
```
:::

### Complex Map

:::code
```js
const map = googleMaps.map()
    .markers(locations, options)
    .styles(styleSet)
    .center(coords)
    .zoom(level)
    .kml(url);
```
```twig
{% set map = googleMaps.map()
    .markers(locations, options)
    .styles(styleSet)
    .center(coords)
    .zoom(level)
    .kml(url) %}
```
```php
$map = GoogleMaps::map()
    ->markers($locations, $options)
    ->styles($styleSet)
    ->center($coords)
    ->zoom($level)
    ->kml($url);
```
:::

You'll notice that we are _chaining_ methods together in order to build a map. There are several methods in the API, which can be chained in any order necessary.

To get a better understanding of how it works, read more on [Chaining](/dynamic-maps/chaining/).

## Supported Languages

The following programming languages are currently supported. To see how to use these methods across each language, click on any of the links below...

### JavaScript

 - [Map Management](/dynamic-maps/basic-map-management/)
 - [Universal Methods](/dynamic-maps/universal-methods/)
 - [Additional JavaScript Methods](/dynamic-maps/javascript-methods/)

### Twig

 - [Map Management](/dynamic-maps/basic-map-management/)
 - [Universal Methods](/dynamic-maps/universal-methods/)
 - [Additional Twig & PHP Methods](/dynamic-maps/twig-php-methods/)

### PHP

 - [Map Management](/dynamic-maps/basic-map-management/)
 - [Universal Methods](/dynamic-maps/universal-methods/)
 - [Additional Twig & PHP Methods](/dynamic-maps/twig-php-methods/)
