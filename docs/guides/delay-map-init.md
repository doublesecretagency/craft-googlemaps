---
description: It's remarkably easy to lazy load a dynamic map. You can optionally suppress the automated initialization, before then initializing the map yourself.

---

# Delaying Map Initialization

You can lazy load a map by simply calling the [`init` method](/javascript/googlemaps.js/#init-mapid-null-callback-null) in JavaScript. As long as the map has been loaded into the DOM, it should be possible to use the JavaScript `init` method to activate it.

:::warning Twig/PHP map creation is okay 👍
Only the `init` method needs to be triggered via JavaScript. The map can still be created via Twig or PHP, you do not need to move the entire map creation process into JavaScript.
:::

When a map is created via Twig or PHP, it will automatically be initialized once the DOM has finished loading. If you do not want to initialize automatically, it's possible to suppress the default behavior.

## Suppress automatic initialization

If you are generating the map via Twig/PHP, you can tell it to skip the default initialization trigger.

:::code
```twig
{{ map.tag({'init': false}) }}
```
```php
$twigMarkup = $map->tag(['init' => false]);
```
:::

For more information, see the `init` option of the [`tag` method...](/dynamic-maps/twig-php-methods/#tag-options)

## Manually initialize the map

Once the DOM has been loaded, you can use JavaScript to manually initialize the new map.

:::code
```js
googleMaps.init();
```
:::

The `init` method can be [configured further](/javascript/googlemaps.js/#map-initialization-methods) if necessary.
