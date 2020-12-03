# Loading JavaScript

:::tip Dynamic Maps Only
This guide is only relevant if you are working with [dynamic maps](/dynamic-maps/). The JavaScript assets described below are not relevant in any other context.
:::

When creating a dynamic map with Twig or PHP, there are a couple of JavaScript files which will get automatically loaded on the front-end. These files are required to create and manage the embedded maps. Similarly, we will include a call to the Google Maps JavaScript API.

Combined, they produce an HTML snippet similar to this...

```html
<script src="https://maps.googleapis.com/maps/api/js?key=[KEY]" defer></script>
<script src="https://yourwebsite.com/cpresources/[HASH]/js/googlemaps.js"></script>
<script src="https://yourwebsite.com/cpresources/[HASH]/js/dynamicmap.js"></script>
```

For various reasons, however, you may not want these files to be loaded automatically. It's possible to suppress the automatic loading of these assets, and then manually load them later.

## Automatically

In addition to the Google Maps API reference, there are two [JavaScript files](/javascript/) which are required whenever a dynamic map is present. For your convenience, these files will be loaded into the page automatically.
 
To prevent them from being automatically loaded, simply set the `js` option to `false` when creating a map.

:::code
```twig
{% set map = googleMaps.map(locations, {
    'js': false
}) %}
```
```php
$map = GoogleMaps::map($locations, [
    'js' => false
]);
```
:::

For more info, see the complete [list of options...](/dynamic-maps/map-management/#map-locations-options)

## Manually

Once you have disabled the automatic loading of these files, you will then be responsible for loading them manually.

:::code
```twig
{# Load the required JavaScript files #}
{% do googleMaps.loadAssets() %}
```
```php
// Load the required JavaScript files
GoogleMaps::loadAssets();
```
:::

:::warning Load with Twig (or PHP) when possible
If you need to load the assets outside of Twig or PHP, your implementation will need to be a bit more customized. It is very likely to be a delicate and frustrating experience. For that reason, we recommend loading the assets via Twig (or PHP) whenever possible.
:::
