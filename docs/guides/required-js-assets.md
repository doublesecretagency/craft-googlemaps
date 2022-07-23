---
description:
---

# Required JS Assets

:::tip Dynamic Maps Only
This guide is only relevant if you are working with [dynamic maps](/dynamic-maps/). The JavaScript assets described below are not relevant in any other context.
:::

To create and manage embedded maps, a few external JavaScript files will be required. It is also necessary for us to load the Google Maps JavaScript API.

Combined, they produce an HTML snippet similar to this...

```html
<script src="https://maps.googleapis.com/maps/api/js?key=[KEY]" defer></script>
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js" defer></script>
<script src="https://yourwebsite.com/cpresources/[HASH]/js/googlemaps.js" defer></script>
<script src="https://yourwebsite.com/cpresources/[HASH]/js/dynamicmap.js" defer></script>
```

:::warning Not seeing these lines in your source code?
Make sure that the `<html>`, `<head>`, and `<body>` tags are properly formed (including their respective closing tags). Craft requires those HTML tags to be correctly formed, in order to inject the necessary JavaScript libraries.
:::

If the map is being rendered by Twig or PHP, these JS files will be [automatically](#loaded-automatically) loaded for you. However, if the map is being rendered exclusively via JavaScript, you will be responsible for loading these JS files [manually](#loaded-manually).

For various reasons, you may not want these files to be loaded automatically. It's possible to [suppress](#disable-automatic-loading) the initial automatic loading of these assets, then manually load them later.

## Loaded Automatically

In addition to the Google Maps API reference, there are two [JavaScript files](/javascript/) which are required whenever a dynamic map is present. For your convenience, these files will be loaded into the page automatically when rendering a map in Twig or PHP.

:::warning No autoloading for JavaScript-only maps
If your map is being rendered exclusively in JavaScript, the required assets will _not_ be loaded automatically. For purely JS maps, ensure that you load the required assets [manually](#loaded-manually).
:::

## Disable Automatic Loading

If necessary, you can prevent the required assets from being loaded automatically.  When the `tag` method is appended, simply set the `assets` value to `false`.

:::code
```twig
{# Bypass default asset loading #}
{{ map.tag({'assets': false}) }}
```
```php
// Bypass default asset loading
$twigMarkup = $map->tag(['assets' => false]);
```
:::

Or if you do want to load the assets, but just need more control over the Google Maps API URL, you can use the `params` option to make further adjustments. This allows you to add [query parameters](https://developers.google.com/maps/documentation/javascript/url-params) to the Google Maps API URL.

:::code
```twig
{# Show map in Japanese #}
{{ map.tag({
    'params': {
        'language': 'ja',
        'region': 'JP'
    }
}) }}
```
```php
// Show map in Japanese
$twigMarkup = $map->tag([
    'params' => [
        'language' => 'ja',
        'region' => 'JP'
    ]
]);
```
:::



For more info, see the complete [list of options...](/dynamic-maps/twig-php-methods/#tag-options)

## Loaded Manually

If you are not automatically loading the required assets, you will instead be responsible for loading them manually. There are several ways to approach this, how you choose to go about it is up to you.

The simplest approach is to use to the `loadAssets` method.

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

The `loadAssets` method will also accept an array of parameters, which will be appended to the Google Maps API URL. Anything that is permissible as an [API URL parameter](https://developers.google.com/maps/documentation/javascript/url-params) is permitted here.

For example, this is how you may apply [Map IDs](https://developers.google.com/maps/documentation/javascript/styling#using_map_ids_in_your_application_code)...

:::code
```twig
{# Append the specified Map ID to the API URL #}
{% do googleMaps.loadAssets({
    'map_ids': '1234'
}) %}
```
```php
// Append the specified Map ID to the API URL
GoogleMaps::loadAssets([
    'map_ids' => '1234'
]);
```
:::

If you really don't want Twig/PHP to load the assets on your behalf, and you are determined to take matters into your own hands, there is one other tool available to you.

The `getAssets` method will retrieve the list of required JS assets as an _array of required URLs_. Once you have those URLs, you are free to load them into the page as you see fit.

:::code
```twig
{# Get an array of required URLs #}
{% set assets = googleMaps.getAssets() %}
```
```php
// Get an array of required URLs
$assets = GoogleMaps::getAssets();
```
:::

Like `loadAssets`, the `getAssets` method can accept an optional array of [query parameters](https://developers.google.com/maps/documentation/javascript/url-params) for the Google Maps API URL.
