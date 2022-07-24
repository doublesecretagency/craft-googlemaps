---
description: A few additional methods are only available in Twig and PHP. Explore what else is possible when working with dynamic maps in Twig and PHP.
---

# Additional Twig & PHP Methods

In addition to all the [Universal Methods](/dynamic-maps/universal-methods/) available in the API, there are a few more methods that are available exclusively in Twig and PHP.

## `tag(options = {})`

**Ends the map chain.** Outputs a `Twig\Markup` object for use in a template.

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

:::warning Same But Different
The `tag` method also exists in [JavaScript](/dynamic-maps/javascript-methods/#tag-parentid-null), but beware that the usage is notably different.
:::

Regardless of whether you are using Twig or PHP, this will create a new `Twig\Markup` object.

If you are working in Twig, you can use curly braces to output the map directly. This produces a `<div>` element with a carefully constructed `data-dna` property.

#### Arguments

 - `options` (_array_) - Configuration options for the map container and its related JavaScript.

| Option     | Type     | Default | Description
|:-----------|:--------:|:-------:|:------------
| `init`     | _bool_   | `true`  | Whether to automatically initialize the map via JavaScript.
| `assets`   | _bool_   | `true`  | Whether to preload the necessary JavaScript assets.
| `inline`   | _bool_   | `false` | Whether to render JS immediately after map `<div>`.
| `callback` | _string_ | `null`  | JavaScript function to run after the map has loaded.
| `params`   | _object_ | `{}`    | Optional [parameters](https://developers.google.com/maps/documentation/javascript/url-params) for the Google Maps API.
| `api`      | _object_ | `{}`    | _[DEPRECATED]_ Use `params` instead.

**The `init` option**

Allows the map to be automatically rendered via JavaScript, after the `<div>` element has first been loaded onto the page via Twig. See how to [disable automatic initialization...](/guides/delay-map-init/)

**The `inline` option**

:::warning Enable for Sprig-loaded maps
If you are using [Sprig](https://plugins.craftcms.com/sprig) (or a similar tool) for DOM manipulation, you will likely need to enable the `inline` option.
:::

This puts all relevant JavaScript **immediately after** the map container, allowing Sprig to refresh the JavaScript each time the `div` is reloaded.  If you leave `inline` disabled, all JavaScript will be injected into the footer by default, beyond the reach of Sprig.

**The `params` option**

If you need to [change the map language and/or region](/guides/changing-map-language/):

:::code
```twig
{{ map.tag({
    'params': {
        'language': 'ja',
        'region': 'JP'
    }
}) }}
```
```php
$twigMarkup = $map->tag([
    'params' => [
        'language' => 'ja',
        'region' => 'JP'
    ]
]);
```
:::

#### Returns

 - A new `Twig\Markup` object. This can be output directly using Twig's curly brace syntax (`{{ }}`).
 
---
---

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
