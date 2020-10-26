# Static Maps

## Basic Examples

Here's how to use a static map in its simplest form...

```twig
{{ googleMaps.img(locations).tag() }}
```

The `img` method will generate a [Static Map Model](/models/static-map-model/). You can create a static map containing a set of markers, then render it as either the `src` attribute or the entire `<img>` tag.

If you only want the `src` generated to fetch the static map, do it like this...

```twig
{{ googleMaps.img(locations).src() }}
```

These methods are equally available in both Twig and PHP.

:::code
```twig
{# Get the entire <img> tag #}
{% set tag = googleMaps.img(locations).tag() %}

{# Get just the `src` attribute value #}
{% set src = googleMaps.img(locations).src() %}
```
```php
// Get the entire <img> tag
$tag = GoogleMaps::img($locations)->tag();

// Get just the `src` attribute value
$src = GoogleMaps::img($locations)->src();
```
:::

## Public Methods

### `img(locations, options = {})`

#### `locations`

 - Location(s) to appear on the map. See the [Locations](/dynamic-maps/locations/) page for detailed information.
 
If you skip the `locations` parameter by passing in _null_, the map will try its best to render without any markers.
 
#### `options`

An object containing any of these optional configurations:

| Option    | Type              | Default     | Description |
|-----------|:-----------------:|:-----------:|-------------|
| `width`   | _int_             | `480`       | Set the width of the map (in px). |
| `height`  | _int_             | `320`       | Set the height of the map (in px). |
| `zoom`    | _int_             | `11`        | Set the zoom level of the map. [(1 - 22)](https://stackoverflow.com/a/32407072/3467557) |
| `center`  | [coords](/models/coordinates/) | automatic   | Set the center position of the map. |
| `maptype` | _string_          | `"roadmap"` | Type of map ("roadmap", "satellite", "hybrid", "terrain"). |
| `scale`   | _int_             | `2`         | 2 = Retina, 1 = Non-retina |
| `imgSrc`  | _int_             | _false_     | If set to _true_, the method will return the map `src` URL directly (instead of the full `<img>` tag). |
| `field`   | _string_\|_array_ | _null_      | Address field(s) to be included on the map. (includes all by default) |

### `tag()`

Returns the static map as a completely rendered `<img>` tag.

### `src()`

Returns the static map as only the `src` attribute. The remainder of the `<img>` tag must be compiled manually.
