# Options

| Option                            | Type     | Default | Description                                      |
|-----------------------------------|:--------:|:-------:|--------------------------------------------------|
| [`target`](#target)               | _mixed_  | `null`  | Center point for the proximity search.           |
| [`range`](#range)                 | _int_    | `500`   | The search radius, measured in `units`.          |
| [`units`](#units)                 | _string_ | `'mi'`  | Unit of measurement, either miles or kilometers. |
| [`subfields`](#subfields)         | _array_  | `null`  | Filter by contents of specific subfields.        |
| [`requireCoords`](#requirecoords) | _bool_   | `false` | Whether results should only include Addresses with valid coordinates. |

## `target`

### Default: `null`

If the `target` is **null**, a target-based proximity search will not be conducted. You will still be able to use the `subfields` and `requireCoords` options effectively. However, the `range` and `units` options will be rendered irrelevant and ineffective.

If the `target` is a **set of [coordinates](/models/coordinates/)**, those coordinates will be directly used as the center point for the proximity search. No API calls will be necessary, the entire proximity search can be handled internally.

If the `target` is a **string** or a **set of parameters**, the plugin will perform an address lookup to determine the center point for the proximity search. Please see the [Geocoding Parameters](/geocoding/parameters/) for more information on what is allowed.

:::tip Region Biasing
Worried about the proximity search starting from the right place? Check out [Region Biasing...](/guides/region-biasing/)
:::

## `range`

### Default: `500`

How wide of an area to conduct a proximity search within. The value represents the search radius, reaching outward from the `target` value. The units of measurement will be defined by `units`.

## `units`

### Default: `'mi'`

The unit of measurement by which to measure distances. Accepts the following values:

 - `'mi'` or `'miles'`
 - `'km'` or `'kilometers'`

## `subfields`

### Default: `null`

The `subfields` option allows you to filter the proximity search results based on specific subfield values of the Address field. It ensures that the query returns only the results which **exactly match** the specified subfield values.

You can specify these filters as a collection of `subfield: value` pairs:

:::code
```twig
'subfields': {
    'city': 'Los Angeles',
    'state': 'CA'
}
```
```php
'subfields' => [
    'city' => 'Los Angeles',
    'state' => 'CA'
]
```
:::

Each subfield can also be specified as an array, which allows multiple valid matches:

:::code
```twig
'subfields': {
    'state': ['CA', 'OR', 'WA']
}
```
```php
'subfields' => [
    'state' => ['CA', 'OR', 'WA']
]
```
:::

Read more about [filtering by subfields...](/guides/filter-by-subfields/)

## `requireCoords`

### Default: `false`

Determines whether a valid set of coordinates is required. If set to `true`, the results will only include Addresses that have valid coordinates.

A set of coordinates is considered valid only if both the `latitude` and `longitude` values are populated.
