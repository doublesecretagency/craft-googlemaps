# Options

| Option       | Type                                       | Default          | Description                                     |
|--------------|:------------------------------------------:|:----------------:|-------------------------------------------------|
| `target`     | _string_, [coords](/models/coordinates/), or _null_ | `null` (autodetect) | Center point for the proximity search. |
| `range`      | _int_                                      | `25`             | The search radius, measured in `units`.         |
| `units`      | _string_                                   | `"miles"`        | Units of measurement ("miles" or "kilometers"). |
| `fields`     | _string_ or _array_                        | `null`           | Only search against the specified field(s).     |
| `subfields`  | _object_                                   | `null`           | An associative array of subfield filters.       |
| `components` | _object_ or _string_                       | `null`           | An associative array of Google Maps components. |

## `target`

### Default: `null`

If the `target` is a **string**, the plugin will perform an [address lookup](/geocoding/) to determine the center point of the proximity search. The string can be anything that translates into a full or partial address. Front-end users will often enter a postal code or partial address.

If the `target` is a set of [coordinates](/models/coordinates/), those coordinates will be used as the center point of the proximity search. No API calls are necessary, the proximity search is handled using internal data.

If the `target` is **null**, the plugin will attempt to use [visitor geolocation](/geolocation/) to determine a reasonable center point for the proximity search.

## `range`

### Default: `25`

How wide of an area to conduct a proximity search within. The value represents the search radius, reaching outward from the `target` value. The units of measurement will be defined by `units`.

## `units`

### Default: `"miles"`

The unit of measurement by which to measure distances. Can be set to either `"miles"` or `"kilometers"`.

## `fields`

### Default: `null`

The `fields` option will restrain the proximity search to only the Address field(s) specified here. If you omit this option, it will conduct the proximity search across _all_ fields in that section by default.

Value can be either a field handle **string**, or an **array** of field handles. If no value is specified, all Address fields will be included by default.

[See how it works...](/guides/filter-by-fields-and-subfields/)

## `subfields`

### Default: `null`

The `subfields` option will exclude results which don't _exactly match_ the specified subfields. It allows you to filter the proximity search results based on existing subfield values of the Address field.

[See how it works...](/guides/filter-by-fields-and-subfields/)

## `components`

### Default: `null`

Additional components that can be passed to the Google Maps API, in order to perform techniques like Region Biasing.

[See how it works...](/guides/region-biasing/)

::: warning ONLY RELEVANT IF TARGET IS A STRING
The `components` option only matters if the `target` value is a **string**. Using a string will force an [address lookup](/geocoding/), which is where the `components` are used in the geocoding process. Otherwise, `components` are irrelevant.
