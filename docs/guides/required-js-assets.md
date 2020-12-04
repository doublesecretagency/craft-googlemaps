# Required JS Assets

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

For various reasons, you may not want these files to be loaded automatically. It's possible to suppress the initial automatic loading of these assets, then manually load them later.

## Loaded Automatically

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

## Loaded Manually

Once you have disabled the automatic loading of these files, you will then be responsible for loading them manually. There are several ways to approach this, how you choose to go about it is up to you.

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

Like `loadAssets`, the `getAssets` method can accept an optional array of [API URL parameters](https://developers.google.com/maps/documentation/javascript/url-params).
