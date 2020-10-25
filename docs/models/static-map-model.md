# Static Map Model

The Static Map Model is used to generate [Static Maps](/static-maps/). Chaining together static map methods is very similar to [chaining dynamic map methods](/dynamic-maps/chaining/).


## Public Methods

### `__construct($locations = [], $options = [])`

This method will be called when a `new StaticMap` is initialized. It creates a starting point which sets the map-building chain in motion. You will be able to build upon the map by adding markers, paths, etc.

:::code
```twig
{% set map = googleMaps.img(locations) %}
```
```php
$map = GoogleMaps::img($locations);
```
:::

:::warning The "map" variable
For each of the remaining examples on this page, the `map` variable will be an instance of a **Static Map Model**. In each example, you will see how map methods can be chained at will.

It will be assumed that the `map` object has already been initialized, as demonstrated above.
:::

Once you have the map object in hand, you can then chain other methods to further customize the map. There is no limit as to how many methods you can chain, nor what order they should be in.

#### Arguments

 - `$locations` (_mixed_) - See a description of acceptable [locations...](/dynamic-maps/locations/)
 - `$options` (_array_) - Optional parameters to configure the map. (see below)
 
| Option          | Type     | Default | Description |
|-----------------|:--------:|:-------:|-------------|
| `id`            | _string_ | <span style="white-space:nowrap">`"map-{random}"`</span> | Set the `id` attribute of the map container. |
| `width`         | _int_    | `640`   | Width of the map, in pixels. (max `640`) |
| `height`        | _int_    | `320`   | Height of the map, in pixels. (max `640`) |
| `attr`          | _array_  | _null_  | Additional attributes for the `<img>` tag. |
| `zoom`          | _int_    | (automatic) | Set the default zoom level of the map. <span style="white-space:nowrap">(`1`-`22`)</span> |
| `center`        | _[coords](/models/coordinates/)_\|_string_ | (automatic) | Center the map. |
| `styles`        | _array_  | _null_  | An array of [map styles](/guides/styling-a-map/). |
| `scale`         | _int_    | `2`     | Pixel density of image. (standard = `1`, retina = `2`) |
| `format`        | _string_ | `png`   | Type of [image format](https://developers.google.com/maps/documentation/maps-static/start#ImageFormats). |
| `maptype`       | _string_ | `roadmap` | Type of [map format](https://developers.google.com/maps/documentation/maps-static/start#MapTypes). |
| `language`      | _string_ | (automatic) | Language for location labels. |
| `region`        | _string_ | _null_  | Adjusts map based on geo-political sensitivities. |
| `visible`       | _[coords](/models/coordinates/)_\|_string_\|_array_ | _null_ | Ensures that the specified point(s) remain visible. |
| `markerOptions` | _array_  | _null_  | Allows any properties accepted by the `markers` method. (see below) |





---
---

### `markers($locations, $options = [])`

Append markers to an existing map object.

#### Arguments

 - `$locations` (_mixed_) - See a description of acceptable [locations...](/dynamic-maps/locations/)
 - `$options` (_array_) - Optional parameters to configure the markers. (see below)
 
| Option               | Type                 | Default | Description |
|----------------------|:--------------------:|:-------:|-------------|
| `size` | _string_ | _null_ | `tiny`, `mid`, or `small` |
| `scale` | _int_ | 1 | Multiplied with size to determine output size. (`1`,`2`,`4`) |
| `color` | _string_ | _null_ | A hex code or [predefined color](https://developers.google.com/maps/documentation/maps-static/start#MarkerStyles). |
| `label` | _string_ | _null_ | A single uppercase alphanumeric character from the set `{A-Z, 0-9}`. |
| `icon` | _string_ | _null_  | An `icon` as defined by [`google.maps.MarkerOptions`](https://developers.google.com/maps/documentation/javascript/reference/marker#MarkerOptions.icon). |
| `anchor` | `'bottom'` | _null_ | Set as `x,y` coordinates or a [predefined alignment](https://developers.google.com/maps/documentation/maps-static/start#CustomIcons). |
| `visible` | _string_ | _null_ | blah |
| `fields`        | _string_ or _array_  | _null_  | Which field(s) of the element(s) should be included on the map. (_null_ will include all Address fields) |

#### Returns

 - _self_ - A chainable self-reference to this `StaticMap` object.

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

### `path($points, $options = [])`

Draw a path on an existing map object.

#### Arguments

 - `$points` (_mixed_) - See a description of acceptable [locations...](/dynamic-maps/locations/)
 - `$options` (_array_) - Optional parameters to configure the path. (see below)
 
| Option      | Type     | Default | Description |
|-------------|:--------:|:-------:|-------------|
| `weight`    | _int_    | 5       | Thickness of the path in pixels. |
| `color`     | _string_ | _null_  | The line [color](https://developers.google.com/maps/documentation/maps-static/start#PathStyles). |
| `fillcolor` | _string_ | _null_  | The fill [color](https://developers.google.com/maps/documentation/maps-static/start#PathStyles). |
| `geodesic`  | _bool_   | _false_ | Whether the path should be interpreted as a geodesic line. |

#### Returns

 - _self_ - A chainable self-reference to this `StaticMap` object.

:::code
```twig
{% do map.path(points) %}
```
```php
$map->path($points);
```
:::

---
---

### `styles($styleSet)`

Style a map based on a given array of styles.

:::warning Generating Styles
For more information on how to generate a set of styles, read [Styling a Map](/guides/styling-a-map/).
:::

#### Arguments

 - `$styleSet` (_array_) - A set of styles to be applied to the map.

#### Returns

 - _self_ - A chainable self-reference to this `StaticMap` object.

:::code
```twig
{% do map.styles(styleSet) %}
```
```php
$map->styles($styleSet);
```
:::

---
---

### `zoom($level)`

Set the map's zoom level.

#### Arguments

 - `$level` (_int_) - The zoom level. Must be an integer between `1` - `22`.

#### Returns

 - _self_ - A chainable self-reference to this `StaticMap` object.

:::code
```twig
{% do map.zoom(level) %}
```
```php
$map->zoom($level);
```
:::

---
---

### `center($coords)`

Center the map.

#### Arguments

 - `$coords` (_[coords](/models/coordinates/)_|_string_) - A key-value set of coordinates, or a comma-separated string value. You can also specify the name of a location to be geocoded (ie: `New York, NY`).

#### Returns

 - _self_ - A chainable self-reference to this `StaticMap` object.

:::code
```twig
{% do map.center(coords) %}
```
```php
$map->center($coords);
```
:::

---
---

### `tag()`

Renders a fully-compiled `<img>` tag using the final `src` output. (see the [`src` method](#src) below)

#### Returns

 - _Markup_ - A Twig Markup instance, ready to be rendered via curly braces.

:::code
```twig
{{ map.tag() }}
```
```php
$twigMarkup = $map->tag();
```
:::

:::warning See also
Internally, the `tag` method relies directly on the `src` method below.
:::

---
---

### `src()`

Compiles the raw `src` attribute value to be used in a corresponding `<img>` tag.

#### Returns

 - _string_ - A string URL of the remote static map source. The Google Maps Static API parses the parameters of this URL to properly compile and display the final map image.  

:::code
```twig
<img src="{{ map.src() }}">
```
```php
$url = $map->src();
```
:::

---
---

### `getDna()`

Get the complete map DNA.

_Aliased as `dna` property via magic method._

#### Returns

 - _array_ - An array of data containing the complete map details.

:::code
```twig
{% set dna = map.dna %}
{% set dna = map.getDna() %}
```
```php
$dna = $map->dna;
$dna = $map->getDna();
```
:::

