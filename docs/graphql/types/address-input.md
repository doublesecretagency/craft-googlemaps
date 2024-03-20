---
description:
---

# GoogleMaps_AddressInput

```graphql
input GoogleMaps_AddressInput {
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

:::warning Same arguments as GoogleMaps_Address
Both types share the same fields. For more details, see the [`GoogleMaps_Address` type](/graphql/types/address/).
:::

The `GoogleMaps_AddressInput` input type should be used in **mutations only**.

:::tip How to write a mutation 
For complete instructions on using the `GoogleMaps_AddressInput` input type in a mutation, read the docs for [Setting an Address](/address-field/graphql/#setting-an-address) with GraphQL.
:::
