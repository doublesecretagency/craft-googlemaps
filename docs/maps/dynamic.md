# Dynamic Maps

## Basic Examples

Here's how to use a dynamic map in its simplest form...

```twig
{{ googleMaps.map(locations).tag() }}
```

The `map` method will generate a [Dynamic Map Model](/models/dynamic-map-model/). You can create a dynamic map containing a set of markers, then render the `<div>` container tag.

## Chaining

There are several methods on the Dynamic Map Model that can be chained together. When combining these methods, it will be possible for you to mix & match information in order to build the map in any way you please.

For complete information, see the page regarding [Chaining...](/maps/chaining/)

## Public Methods

### `map(locations, options = {})`

#### `locations`

Any of the following will be considered valid `locations`:

 - An array of elements
 - An individual element (ie: an Entry)
 - An array of [Address Models](/models/address-model/)
 - An individual [Address Model](/models/address-model/)
 
If you skip the `locations` parameter by passing in _null_, the map will try its best to render without any markers.
 
#### `options`

An object containing any of these optional configurations:

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

### `tag()`

Returns the dynamic map as a completely rendered `<div>` tag.
