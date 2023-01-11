---
description: Learn how to get or set the value of an Address field using GraphQL.
---

# Using an Address in GraphQL

## Getting an Address

When you query an Address via GraphQL, it might look something like this...

```graphql
{
  entries(section: "locations") {
    ... on locations_locations_Entry {
      id
      title
      myAddressField {
        name
        formatted
        lat
        lng
      }
    }
  }
}
```

The example above shows a query that will...

1. Get all Entries from the `locations` section.
2. Get the `id` and `title` of each Entry.
3. Get the data for `myAddressField` (using the handle of your Address field).
4. Within the Address field, you can select whichever subfields are appropriate for your situation.

:::warning Which subfields are available?
Check out the [`GoogleMaps_Address` type](/graphql/types/address/) to see the complete list of arguments.
:::

## Setting an Address

To set an Address value via GraphQL, you may use something like the following...

```graphql
mutation setAddress($id: ID, $myAddressField: GoogleMaps_AddressInput) {
  save_locations_locations_Entry(id: $id, myAddressField: $myAddressField) {
    ... on locations_locations_Entry {
      id
      title
      myAddressField {
        name
        formatted
        lat
        lng
      }
    }
  }
}
```
```graphql
# Query Variables
{
  "id": 101,
  "myAddressField": {
    "street1": "123 Main St",
    "street2": "",
    "city": "Evanston",
    "state": "WY",
    "zip": "82930",
    "country": "United States",
    "lat": 41.2614458,
    "lng": -110.9574582,
  }
}
```

The example above shows a mutation that will...

1. Update the specific Entry with a matching `id`.
2. Set the data for `myAddressField` (using the handle of your Address field).
3. Perform the same query from our previous example (see above).

:::tip More Info
For more information, see the [`GoogleMaps_AddressInput` input type](/graphql/types/address-input/).
:::
