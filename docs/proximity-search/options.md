# Options

| Option       | Type                | Default | Description                                            |
|--------------|:-------------------:|:-------:|--------------------------------------------------------|
| `target`     | _mixed_             | `null`  | Center point for the proximity search.                 |
| `range`      | _int_               | `500`   | The search radius, measured in `units`.                |
| `units`      | _string_            | `'mi'`  | Unit of measurement, either miles or kilometers.       |
| `fields`     | _string_ or _array_ | `null`  | Filter by specified Address field(s).                  |
| `subfields`  | _array_             | `null`  | Filter by contents of specific subfields.              |
| `hasCoords`  | _bool_              | `false` | Whether to exclude Addresses with invalid coordinates. |

## `target`

### Default: `null`

If the `target` is **null**, the plugin will automatically attempt to use [Visitor Geolocation](/geolocation/) in order to determine a reasonable center point for the proximity search.

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

## `fields`

### Default: `null`

The `fields` option will restrain the proximity search to only the specified Address field(s). The value can be either a field handle **string**, or an **array** of field handles.

 - `'businessAddress'`
 - `['businessAddress', 'homeAddress']`

If you omit this option, the proximity search will be conducted across _all_ Address fields in that section by default.

[See how it works...](/guides/filter-by-fields-and-subfields/)

## `subfields`

### Default: `null`

The `subfields` option will exclude results which don't _exactly match_ the specified subfields. It allows you to filter the proximity search results based on existing subfield values of the Address field. The value must be an array of `subfield => value` pairs:

:::code
```twig
'subfields': {
    'city': 'Los Angeles',
    'state': 'CA',
}
```
```php
'subfields' => [
    'city' => 'Los Angeles',
    'state' => 'CA',
]
```
:::

The filter value of each subfield can also be an array if necessary:

:::code
```twig
'subfields': {
    'state': ['CA', 'OR', 'WA'],
}
```
```php
'subfields' => [
    'state' => ['CA', 'OR', 'WA'],
]
```
:::

[See how it works...](/guides/filter-by-fields-and-subfields/)

## `hasCoords`

### Default: `false`

If set to `true`, all addresses with invalid coordinates will be omitted from the results. A set of coordinates is only considered valid if both the `latitude` and `longitude` values are populated. 
