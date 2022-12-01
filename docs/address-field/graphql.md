---
description: When using GraphQL, it is possible to retrieve the complete Address Model from an Address field.
---

# Using an Address in GraphQL

## Example

When you retrieve an Address via GraphQL, it might look something like this...

```graphql
{
  entries(section: "locations") {
    ... on locations_locations_Entry {
      myAddressField {
        formatted
        name
        street1
        street2
        city
        state
        zip
        country
        lat
        lng
      }
    }
  }
}
```

The example above shows a query that will...

1. Get all Entries from the `locations` section.
2. On the `locations` entry type...
   - Access the data for `myAddressField` (use the handle of your Address field).
3. Within the Address field, select several sub fields.

:::warning What sub fields are available?
Check out the [`GoogleMaps_Address` custom type](/graphql/address-type/) to see the complete list of sub fields.
:::

## Custom Types

The Google Maps plugin introduces several new types. However, unless you need to dive deeply into the `GoogleMaps_Raw` type, the only type that will matter is the `GoogleMaps_Address`.

- [**`GoogleMaps_Address`**](/graphql/address-type/)
- [`GoogleMaps_Raw`](/graphql/raw-type/)
- [`GoogleMaps_Component`](/graphql/component-type/)
- [`GoogleMaps_Geometry`](/graphql/geometry-type/)
- [`GoogleMaps_Location`](/graphql/location-type/)
