---
description: Make your maps blend seamlessly with any website, by easily adding your own custom styles.
meta:
- property: og:type
  content: website
- property: og:url
  content: https://plugins.doublesecretagency.com/google-maps/guides/styling-a-map/
- property: og:title
  content: Styling a Map | Google Maps plugin for Craft CMS
- property: og:description
  content: Make your maps blend seamlessly with any website, by easily adding your own custom styles.
- property: og:image
  content: https://plugins.doublesecretagency.com/google-maps/images/meta/styling-a-map.png
- property: twitter:card
  content: summary_large_image
- property: twitter:url
  content: https://plugins.doublesecretagency.com/google-maps/guides/styling-a-map/
- property: twitter:title
  content: Styling a Map | Google Maps plugin for Craft CMS
- property: twitter:description
  content: Make your maps blend seamlessly with any website, by easily adding your own custom styles.
- property: twitter:image
  content: https://plugins.doublesecretagency.com/google-maps/images/meta/styling-a-map.png
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
// Set of map styles, formatted as a JSON array
const styleSet = [...];

// Apply styles to the map
const map = googleMaps.map(locations, {
    'styles': styleSet
});
```
```twig
{# Set of map styles, formatted as a JSON array #}
{% set styleSet = [...] %}

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
const map = googleMaps.getMap(mapId);

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
