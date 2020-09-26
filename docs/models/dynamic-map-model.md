# Dynamic Map Model

The Dynamic Map Model is critical for generating a [Dynamic Map](/maps/dynamic/). Thanks to the magic of [chaining](/maps/chaining/), it allows you to build maps that are as complex as they need to be.

## Public Methods

### `__construct($locations = [], $options = [])`

This method will be called when you initialize a `new DynamicMap`. It creates a starting point which sets the map-building chain in motion. You will be able to build upon the map by adding markers, KML layers, etc.

:::code
```twig
{# Using a helper method to wrap `new DynamicMap` #}
{% set map = googleMaps.map(locations) %}
```
```php
// Using a helper method to wrap `new DynamicMap`
$map = GoogleMaps::map($locations);
```
:::

:::warning The "map" variable
For each of the remaining examples on this page, the `map` variable will be an instance of a **Dynamic Map Model**. In each example, you will see how map methods can be chained at will.

It will be assumed that the `map` object has already been initialized, as demonstrated above.
:::

Once you have the map object in hand, you can then chain other methods to further customize the map. There is no limit as to how many methods you can chain, nor what order they should be in.

#### Arguments

 - `$locations` (_mixed_) - See a description of acceptable [locations...](/maps/locations/)
 - `$options` (_array_) - Optional parameters to configure the map. (see below)

| Option               | Type                | Default            | Description |
|----------------------|:-------------------:|:------------------:|-------------|
| `id`                 | _string_            | <span style="white-space:nowrap">`"map-{random}"`</span> | Set the `id` attribute of the map container. |
| `width`              | _int_               | _null_             | Set the width of the map (in px). |
| `height`             | _int_               | _null_             | Set the height of the map (in px). |
| `zoom`               | _int_               | (uses `fitBounds`) | Set the default zoom level of the map <span style="white-space:nowrap">(`1`-`22`)</span>. |
| `center`             | [coords](/models/coordinates/) | (uses `fitBounds`) | Set the center position of the map. |
| `styles`             | _array_             | _null_             | An array of [map styles](/guides/styling-a-map/). |
| `mapOptions`         | _object_            | _null_             | Accepts any [`google.maps.MapOptions`](https://developers.google.com/maps/documentation/javascript/reference/map#MapOptions) properties. |
| `markerOptions`      | _object_            | _null_             | Accepts any [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions) properties. |
| `infoWindowOptions`  | _object_            | _null_             | Accepts any [`google.maps.InfoWindowOptions`](https://developers.google.com/maps/documentation/javascript/reference/info-window#InfoWindowOptions) properties. |
| `infoWindowTemplate` | _string_            | _null_             | Template path to use for creating [info windows](/maps/info-windows/). |
| `fields`             | _string_ or _array_ | _null_             | Which field(s) of the element(s) should be included on the map. (includes all Address fields by default) |
| `js`                 | _bool_               | _true_             | Whether to preload the necessary external JavaScript. |


#### Returns

_self_ - This instance of the Dynamic Map Model. By returning a static self reference, chaining is possible.

:::tip Locations are Skippable
If you skip the `locations` parameter, a blank map will be created.
:::

---
---

### `markers($locations, $options = [])`

Append markers to an existing map object.

#### Arguments

 - `$locations` (_mixed_) - See a description of acceptable [locations...](/maps/locations/)
 - `$options` (_array_) - Optional parameters to configure the markers. (see below)
 
| Option               | Type                 | Default | Description |
|----------------------|:--------------------:|:-------:|-------------|
| `id`                 | _string_             | <span style="white-space:nowrap">`"marker-{random}"`</span> | Reference point for each marker. |
| `icon`               | _object_ or _string_ | _null_  | An `icon` as defined by [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions.icon). |
| `markerOptions`      | _object_             | _null_  | Accepts any [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions) properties. |
| `infoWindowOptions`  | _object_             | _null_  | Accepts any [`google.maps.InfoWindowOptions`](https://developers.google.com/maps/documentation/javascript/reference/info-window#InfoWindowOptions) properties. |
| `infoWindowTemplate` | _string_             | _null_  | Template path to use for creating [info windows](/maps/info-windows/). |
| `fields`             | _string_ or _array_  | _null_  | Which field(s) of the element(s) should be included on the map. (_null_ will include all Address fields) |

#### Returns

_self_ - This instance of the Dynamic Map Model. By returning a static self reference, chaining is possible.

:::code
```twig
{% do map.markers(locations) %}
```
```php
$map->markers($locations);
```
:::

---
---

### `kml($files, $options = [])`

Append one or more KML layers to an existing map object.

#### Arguments

 - `$files` (_mixed_)
 - `$options` (_array_) - Optional parameters to configure the KML layers. (see below)
 
| Option             | Type     | Default | Description |
|--------------------|:--------:|:-------:|-------------|
| `id`               | _string_ | <span style="white-space:nowrap">`"kml-{random}"`</span> | Reference point for each KML layer. |
| `kmlLayerOptions`  | _object_ | _null_  | Accepts any [`google.maps.KmlLayerOptions`](https://developers.google.com/maps/documentation/javascript/reference/kml#KmlLayerOptions) properties. |

#### Returns

_self_ - This instance of the Dynamic Map Model. By returning a static self reference, chaining is possible.

:::code
```twig
{% do map.kml(files) %}
```
```php
$map->kml($files);
```
:::

---
---

### `styles($stylesArray)`

Style a map based on a given array of styles.

:::tip Generating Styles
There are many ways to generate an array of map styles. The most popular approach is to use a service like one of the following:

 - example 1
 - example 2
:::

#### Arguments

 - `$stylesArray` (_array_) - A set of styles to be applied to the map.

#### Returns

_self_ - This instance of the Dynamic Map Model. By returning a static self reference, chaining is possible.

:::code
```twig
{% do map.styles(stylesArray) %}
```
```php
$map->styles($stylesArray);
```
:::

---
---

### `html($init = true)`

:::warning LD TODO
Is "tag" better than "html"?
 - Shorter
 - Easier to pronounce
 - Consistent with `img`
:::

Render the necessary `<div>` container to hold the map.

#### Arguments

 - `$init` (_bool_) - Whether to automatically initialize the map DNA via JavaScript. If this is set to `false`, the map DNA will need to be manually initialized at some point in the future.

#### Returns

_Markup_ - A Twig Markup instance, ready to be rendered via curly braces (`{{ }}`).

:::code
```twig
{{ map.tag() }}
```
```php
$twigMarkup = $map->tag();
```
:::
