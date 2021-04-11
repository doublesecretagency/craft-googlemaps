# Options

| Option                            | Type     | Default | Description                                      |
|-----------------------------------|:--------:|:-------:|--------------------------------------------------|
| [`target`](#target)               | _mixed_  | `null`  | Center point for the proximity search.           |
| [`range`](#range)                 | _int_    | `500`   | The search radius, measured in `units`.          |
| [`units`](#units)                 | _string_ | `'mi'`  | Unit of measurement, either miles or kilometers. |
| [`subfields`](#subfields)         | _mixed_  | `null`  | Filter by contents of specific subfields.        |
| [`requireCoords`](#requirecoords) | _bool_   | `false` | Whether results should only include Addresses with valid coordinates. |

## `target`

If the target is **null**...
 - No target-based proximity search will be conducted. The `range` and `units` options will be rendered moot. You may still use the `subfields` and `requireCoords` options to narrow the query results, even without a specified target.

If the target is a **set of [coordinates](/models/coordinates/)**...
 - Those coordinates will be directly used as the starting point for the proximity search. No API calls will be necessary, since the entire proximity search can be handled internally.

If the target is a **string** or a **set of parameters**...
 - An internal [address lookup](/geocoding/) will be performed to determine the center point of the proximity search. Please see the [Geocoding Target](/geocoding/target/) for more information on what is allowed.

:::tip Region Biasing
Worried about the proximity search starting from the right place? Check out [Region Biasing...](/guides/region-biasing/)
:::

## `range`

How wide of an area to conduct a proximity search within. The value represents the search radius, reaching outward from the `target` value. The units of measurement will be defined by `units`.

:::code
```twig
{% set options = {
    'range': 50,
    'units': 'kilometers',
} %}
```
```php
$options = [
    'range' => 50,
    'units' => 'kilometers',
];
```
:::

## `units`

The unit of measurement by which to measure distances. Accepts the following values:

 - `'mi'` or `'miles'`
 - `'km'` or `'kilometers'`

## `subfields`

The `subfields` option allows you to filter the proximity search results based on specific subfield values of the Address field. It ensures that the query returns only the results which **exactly match** the specified subfield values.

The value can be specified as an array of key-value pairs, or as the specific string **"fallback"**. For more information, see the docs regarding [filtering by subfields](/guides/filter-by-subfields/).

:::warning Subfield Filter Fallback
When conducting proximity searches across a broad area, you may find it helpful to enable the [subfield filter fallback](/guides/filter-by-subfields/#subfield-filter-fallback) mechanism.
:::

## `requireCoords`

Determines whether a valid set of coordinates is required. If set to `true`, the results will only include Addresses which have valid coordinates.

A set of coordinates is considered valid only if both the `latitude` and `longitude` values are populated.
