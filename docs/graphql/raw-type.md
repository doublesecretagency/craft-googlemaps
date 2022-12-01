---
description:
---

# Raw Type

:::tip Limited Usage
Only appears within the [`GoogleMaps_Address`](/graphql/address-type/) type.
:::

```graphql
type GoogleMaps_Raw {
  address_components: [GoogleMaps_Component]
  formatted_address: String
  geometry: GoogleMaps_Geometry
  name: String
  place_id: String
  html_attributions: [String]
}
```

### `address_components`

_\[[GoogleMaps_Component](/graphql/component-type/)]_ - An array containing the separate components applicable to this address.

### `formatted_address`

_String_ - A string containing the human-readable address of this location.

### `geometry`

[_GoogleMaps_Geometry_](/graphql/geometry-type/) - The place's geometry-related information.

### `name`

_String_ - The place's name.

### `place_id`

_String_ - A textual identifier that uniquely identifies a place.

### `html_attributions`

_\[String]_ - An array of attributions to display when showing the search results.
