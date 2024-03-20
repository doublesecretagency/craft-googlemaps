---
description:
---

# GoogleMaps_Address

```graphql
type GoogleMaps_Address {
  id: Int
  elementId: Int
  fieldId: Int
  formatted: String
  raw: GoogleMaps_Raw
  name: String
  street1: String
  street2: String
  city: String
  state: String
  zip: String
  neighborhood: String
  county: String
  country: String
  countryCode: String
  placeId: String
  distance: Float
  zoom: Int
  lat: Float
  lng: Float
}
```

:::warning For queries only
To edit an Address field via a mutation, see the [`GoogleMaps_AddressInput` input](/graphql/types/address-input/).
:::

:::tip How to write a query
For complete instructions on using the `GoogleMaps_Address` type in a query, read the docs for [Getting an Address](/address-field/graphql/#getting-an-address) with GraphQL.
:::

## Arguments

### `id`

_Int_ - ID of the Address.

### `elementId`

_Int_ - Element ID of the element containing the Address.

### `fieldId`

_Int_ - Field ID of the Address field.

### `formatted`

_String_ - A nicely-formatted single-line interpretation of the address, provided by Google during the initial geocoding.

### `raw`

_GoogleMaps_Raw_ - The original data used to create this Address Model. Contains the full JSON response from the original Google API call.

### `name`

_String_ - The location's official name. Commonly used for landmarks and business names.

### `street1`

_String_ - The first line of the street address. Usually contains the street name & number of the location.

### `street2`

_String_ - The second line of the street address. Usually contains the apartment, unit, or suite number.

### `city`

_String_ - The city. (aka: town)

### `state`

_String_ - The state. (aka: province)

### `zip`

_String_ - The zip code. (aka: postal code)

### `neighborhood`

_String_ - The neighborhood.

### `county`

_String_ - The local county. (aka: district)

### `country`

_String_ - The country. (aka: nation)

### `countryCode`

_String_ - The country code.

### `placeId`

_String_ - The official `place_id` as specified by the Google API.

### `distance`

_Float_ - The distance between the Address and a proximity search target. If not conducting a proximity search, value will be `null`.

### `zoom`

_Int_ - Zoom level of the map as shown in the control panel.

### `lat`

_Float_ - Latitude of location.

### `lng`

_Float_ - Longitude of location.
