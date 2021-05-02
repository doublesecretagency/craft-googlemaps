---
description:
---

# Styling a Map

It's very common for people to want their maps to look a specific way. In fact, there are services which allow you to customize a map however you see fit. These services will generate a JSON snippet containing all of your styles.

Here are a few services which can help you create a collection of custom styles...

 - [Google Styling Wizard](https://mapstyle.withgoogle.com)
 - [Snazzy Maps](https://snazzymaps.com/)
 
Once you have generated your map styles, copy the JSON snippet and apply it to your map.

## Styling a new map

You can specify the `styles` option when you create the initial map...

:::code
```js
// Set of map styles, formatted as a JSON object
var styleSet = {...};

// Apply styles to the map
var map = googleMaps.map(locations, {
    'styles': styleSet
});
```
```twig
{# Set of map styles, formatted as a JSON object #}
{% set styleSet = {...} %}

{# Apply styles to the map #}
{% set map = googleMaps.map(locations, {
    'styles': styleSet
}) %}
```
```php
// Set of map styles, formatted as a JSON array
$styleSet = [...];

// Apply styles to the map
$map = GoogleMaps::map($locations, [
    'styles' => $styleSet
]);
```
:::

## Styling an existing map

You can also apply styles to an existing map...

:::code
```js
// Get an existing map
var map = googleMaps.getMap(mapId);

// Style the map
map.styles(styleSet);
```
```twig
{# Get an existing map #}
{% set map = googleMaps.getMap(mapId) %}

{# Style the map #}
{% do map.styles(styleSet) %}
```
```php
// Get an existing map
$map = GoogleMaps::getMap($mapId);

// Style the map
$map->styles($styleSet);
```
:::
