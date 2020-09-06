# Dynamic Maps (EXTRA CODE)

:::code
```twig
{# Get the entire <div> tag #}
{% set tag = googleMaps.map(locations).tag() %}
```
```php
// Get the entire <div> tag
$tag = GoogleMaps::map($locations)->tag();
```
:::

-----

Here is a general example of how you'd use the method...

```twig
{% set options = {
    height: 300,
    zoom: 4,
    mapOptions: {
        gestureHandling: 'none',
        mapTypeId: 'google.maps.MapTypeId.HYBRID'
    },
    markerOptions: {
        icon: {
            url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
            scaledSize: 'new google.maps.Size(32,32)'
        }
    },
    infoWindowOptions: {
        maxWidth: 200
    },
    infoWindowTemplate: 'path/to/info-window.twig'
} %}

{{ googleMaps.dynamic(locations, options) }}
```

The `mapOptions`, `markerOptions`, and `infoWindowOptions` values will be passed directly through to the Google Maps API. Whatever values you provide in Twig will be converted directly into JavaScript.

::: warning JAVASCRIPT OBJECTS
All `google.maps` values need to be enclosed in strings. At runtime, Twig will parse each of them down as ordinary JavaScript.

For more information, see [Complex JS in Twig...](/guides/complex-js-in-twig/)
:::

::: tip GESTUREHANDLING
Setting the `gestureHandling` to `"none"` will have two notable effects...

 - The _scrollwheel_ of your mouse will no longer zoom the map in & out.
 - The map will no longer be _draggable_.
 
```js
    mapOptions: {
        gestureHandling: 'none'
    }
```
 
See the full details about `gestureHandling` in the [Google documentation...](https://developers.google.com/maps/documentation/javascript/reference/map#MapOptions.gestureHandling)
:::
