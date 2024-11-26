---
description:
---

# Prevent Zoom When Scrolling

It is a common frustration that a map will zoom in or out as you scroll past it. This is a native behavior of Google Maps, but it can be managed via the `gestureHandling` parameter of the `mapOptions` value.

**Restrict Gesture Handling**

:::code
```js
const options = {
    'mapOptions': {
        'gestureHandling': 'cooperative'
    }
};

const map = googleMaps.map(locations, options);
```
```twig
{% set options = {
    'mapOptions': {
        'gestureHandling': 'cooperative'
    }
} %}

{% set map = googleMaps.map(locations, options) %}
```
```php
$options = [
    'mapOptions' => [
        'gestureHandling' => 'cooperative'
    ]
];

$map = GoogleMaps::map($locations, $options);
```
:::

Within the context of the `mapOptions` value, you can indirectly pass a multitude of settings to the Google API. Consult the [Google documentation](https://developers.google.com/maps/documentation/javascript/reference/map#MapOptions.gestureHandling) for complete details regarding `gestureHandling`.
