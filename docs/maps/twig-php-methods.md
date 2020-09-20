# Twig & PHP Methods

In addition to all the [Universal Methods](/maps/universal-methods/) available in the API, there is only one method that is available exclusively in Twig and PHP.

This method has no JavaScript equivalent because it is unnecessary within the JavaScript context.

## `getDna()` (magically aliased as `dna` property)

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

 - An array of data containing the complete map details. It is used to hydrate a map's container in the DOM.
