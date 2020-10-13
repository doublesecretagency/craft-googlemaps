# API

## Universal API

In an effort to smooth the development process, you can effectively call the exact same methods across various programming languages. Whether you are working in JavaScript, Twig, or PHP, the commands to create a map are all nearly identical.

Take a look at the following examples to see how the same concepts translate across different languages.

## Practical Examples

Switch between languages to see the similarities...

### Basic Map

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

### Complex Map

:::code
```js
var map = googleMaps.map()
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

The following programming languages are currently supported:

:::warning JavaScript
 - [Universal Methods](/dynamic-maps/universal-methods/)
 - [Additional JavaScript Methods](/dynamic-maps/javascript-methods/)
:::

:::warning Twig
 - [Universal Methods](/dynamic-maps/universal-methods/)
 - [Additional Twig & PHP Methods](/dynamic-maps/twig-php-methods/)
:::

:::warning PHP
 - [Universal Methods](/dynamic-maps/universal-methods/)
 - [Additional Twig & PHP Methods](/dynamic-maps/twig-php-methods/)
:::
