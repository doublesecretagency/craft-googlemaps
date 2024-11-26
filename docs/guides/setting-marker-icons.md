---
description:
---

# Setting Marker Icons

To customize a marker icon, you can set either a **string** or an **array of [options](https://developers.google.com/maps/documentation/javascript/reference/marker#Icon)**.

1. **As a string** - Specify only the path to your marker icon file.

:::code
```js
map.markers(locations, {
    'icon': '/path/to/marker.png'
});
```
```twig
{% do map.markers(locations, {
    'icon': '/path/to/marker.png'
}) %}
```
```php
$map->markers($locations, [
    'icon' => '/path/to/marker.png'
]);
```
:::

2. **As an array of options** - Specify an array of parameters from Google's native [Icon interface](https://developers.google.com/maps/documentation/javascript/reference/marker#Icon).

:::code
```js
map.markers(locations, {
    'icon': {
        'url': '/path/to/marker.png'
    }
});
```
```twig
{% do map.markers(locations, {
    'icon': {
        'url': '/path/to/marker.png'
    }
}) %}
```
```php
$map->markers($locations, [
    'icon' => [
        'url' => '/path/to/marker.png'
    ]
]);
```
:::

:::warning Google Maps API Documentation
For more information, see the [Google Maps API docs...](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions.icon)
:::

## Control the size of a marker icon

Configure the icon as an array, specifying `scaledSize` to control the marker size.

:::code
```js
// Specify the dimensions of a marker icon
map.markers(locations, {
    'icon': {
        'url': '/path/to/marker.png',
        'scaledSize': {'width': 30, 'height': 40}
    }
});
```
```twig
{# Specify the dimensions of a marker icon #}
{% do map.markers(locations, {
    'icon': {
        'url': '/path/to/marker.png',
        'scaledSize': {'width': 30, 'height': 40}
    }
}) %}
```
```php
// Specify the dimensions of a marker icon
$map->markers($locations, [
    'icon' => [
        'url' => '/path/to/marker.png',
        'scaledSize' => ['width' => 30, 'height' => 40]
    ]
]);
```
:::

---
---

## Set icons for a batch of markers

If you've got multiple groups of markers, you can specify a different icon for each batch of markers.

:::code
```js
// Get all bars & restaurants
const bars        = {'lat': 37.2430548, 'lng': -115.7930198}; // Coords only in JS
const restaurants = {'lat': 57.3009274, 'lng':   -4.4496567}; // Coords only in JS

// Create a dynamic map (with no markers)
const map = googleMaps.map();

// Add all bar markers
map.markers(bars, {
    'icon': '/images/bar-icon.png'
});

// Add all restaurant markers
map.markers(restaurants, {
    'icon': '/images/restaurant-icon.png'
});

// Display map (inject into `#my-map-container`)
map.tag({'parentId': 'my-map-container'});
```
```twig
{# Get all bars & restaurants #}
{% set bars        = craft.entries.section('locations').type('bars').all() %}
{% set restaurants = craft.entries.section('locations').type('restaurants').all() %}

{# Create a dynamic map (with no markers) #}
{% set map = googleMaps.map() %}

{# Add all bar markers #}
{% do map.markers(bars, {
    'icon': '/images/bar-icon.png'
}) %}

{# Add all restaurant markers #}
{% do map.markers(restaurants, {
    'icon': '/images/restaurant-icon.png'
}) %}

{# Display map #}
{{ map.tag() }}
```
```php
// Get all bars & restaurants
$bars        = Entry::find()->section('locations')->type('bars')->all();
$restaurants = Entry::find()->section('locations')->type('restaurants')->all();

// Create a dynamic map (with no markers)
$map = GoogleMaps::map();

// Add all bar markers
$map->markers($bars, [
    'icon' => '/images/bar-icon.png'
]);

// Add all restaurant markers
$map->markers($restaurants, [
    'icon' => '/images/restaurant-icon.png'
]);

// Display map
$twigMarkup = $map->tag();
```
:::

---
---

## Set icon for an existing marker

It's also possible to change the icon of an existing marker:

:::code
```js
map.setMarkerIcon(markerId, icon);
```
```twig
{% do map.setMarkerIcon(markerId, icon) %}
```
```php
$map->setMarkerIcon($markerId, $icon);
```
:::

:::warning Marker ID formula
The default formula for a `markerId` is as follows:

```js
    '[ELEMENT ID]-[FIELD HANDLE]' // eg: '101-myAddressField'
```
:::

Read more about the [`setMarkerIcon` method](/dynamic-maps/universal-methods/#setmarkericon-markerid-icon).

---
---

:::tip Get the Marker IDs
To see the existing marker IDs (if you didn't manually specify them), do the following:

1. Put the site into [devMode](https://craftcms.com/docs/3.x/config/config-settings.html#devmode).
2. View the JS console while the map is being rendered.

In the JavaScript console, you should see a complete play-by-play of every map component being created. Simply copy & paste the marker ID's you need from there, or take note of the pattern for your own needs.
:::
