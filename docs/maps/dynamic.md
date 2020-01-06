# Dynamic Maps

You can create a dynamic map which contains as many markers as you'd like.

## googleMaps.dynamic

``` twig
{{ googleMaps.dynamic(locations, options = {}) }}
```

### `locations`

Can be any of the following...

 - An individual Address Model
 - An array of Address Models
 - An individual element (ie: an Entry)
 - An array of elements
 
If you skip the `locations` parameter by passing in _null_, the map will try its best to render without any markers.
 
### `options`

An object containing any of the following...

| Option               | Type                | Default            | Description |
|----------------------|:-------------------:|:------------------:|------------|
| `id`                 | _string_            | <span style="white-space:nowrap">`"gm-map-1"`</span> | Set the `id` attribute of the map container. |
| `width`              | _int_               | _null_             | Set the width of the map (in px). |
| `height`             | _int_               | _null_             | Set the height of the map (in px). |
| `zoom`               | _int_               | (uses `fitBounds`) | Set the default zoom level of the map. <span style="white-space:nowrap">(`1` - `16`)</span> |
| `center`             | [coords](/models/coordinates/) | (uses `fitBounds`) | Set the center position of the map. |
| `styles`             | _array_             | _null_             | An array of [map styles](/guides/styling-a-map/). |
| `mapOptions`         | _object_            | _null_             | Accepts any [`google.maps.MapOptions`](https://developers.google.com/maps/documentation/javascript/reference/map#MapOptions) properties. |
| `markerOptions`      | _object_            | _null_             | Accepts any [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions) properties. |
| `infoWindowOptions`  | _object_            | _null_             | Accepts any [`google.maps.InfoWindowOptions`](https://developers.google.com/maps/documentation/javascript/reference/info-window#InfoWindowOptions) properties. |
| `infoWindowTemplate` | _string_            | _null_             | Template path to use for creating [info windows](/maps/info-windows/). |
| `fields`             | _string_ or _array_ | _null_             | Which field(s) of the element(s) should be included on the map. (_null_ will include all Address fields) |

## Basic Example

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
```js
mapOptions: {
    gestureHandling: 'none'
}
```

Setting the `gestureHandling` to `"none"` will have two notable effects...

 - The _scrollwheel_ of your mouse will no longer zoom the map in & out.
 - The map will no longer be _draggable_.
 
See the full details about `gestureHandling` in the [Google documentation...](https://developers.google.com/maps/documentation/javascript/reference/map#MapOptions.gestureHandling)
:::
