# Twig & PHP Methods

In addition to all the [Universal Methods](/maps/universal-methods/) available in the API, there are a few more methods that are available exclusively in Twig and PHP.

## `getDna()`

_Aliased as `dna` property via magic method._

:::code
```twig
{% set dna = map.dna %}
{% set dna = map.getDna() %}
```
```php
$dna = $map->dna;
$dna = $map->getDna();
```
:::

#### Returns

 - An array of data containing the complete map details. It will be used to hydrate a map's container in the DOM.

## `tag(autorender = true)`

:::code
```twig
{# Renders a map in the Twig template #}
{{ map.tag() }}
```
```php
// Creates a new Twig\Markup object
$twigMarkup = $map->tag();
```
:::

Even though the `tag` method exists in multiple languages, the usage will be different for each.

:::warning Output
Regardless of whether you are using Twig or PHP, this will create a new `Twig\Markup` object.

If you are working in Twig, you can use curly braces to output the map directly.
:::

#### `autorender`

Set to `true` by default. Allows the map to be automatically rendered via JavaScript, after the `<div>` element has first been loaded onto the page via Twig.

Setting this to `false` will prevent the JavaScript `init` method from automatically firing. You will be responsible for triggering a map render at some point in the future via the JavaScript `init` method.

#### Returns

 - A new `Twig\Markup` object. This can be output directly using Twig's curly brace syntax (`{{ }}`).
