---
description:
---

# 🔧 Styling a Map

<update-message/>

Styling a map is now a much more flexible process. Here is a simple example in JavaScript...

```js
// OLD
smartMap.styleMap('my-map', styleSet);

// NEW
googleMaps.getMap('my-map').styles(styleSet);
```

In addition, you can now apply the styles via JavaScript, Twig, or even PHP.

## Declare styles when creating the map

When creating a map object, you can inject a set of styles immediately.

:::code
```js
const map = googleMaps.map(locations, {
    'styles': styleSet
});
```
```twig
{% set map = googleMaps.map(locations, {
    'styles': styleSet
}) %}
```
```php
$map = GoogleMaps::map($locations, [
    'styles' => $styleSet
]);
```
:::

## Apply styles after map creation

If you have an existing map object, you can apply styles retroactively.

:::code
```js
map.styles(styleSet);
```
```twig
{% do map.styles(styleSet) %}
```
```php
$map->styles($styleSet);
```
:::

:::tip New Documentation
See the complete new [Styling a Map](/guides/styling-a-map/) documentation.
:::
