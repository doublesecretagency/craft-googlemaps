---
description:
---

# Component Type

:::tip Limited Usage
Only appears within the [`GoogleMaps_Raw`](/graphql/raw-type/) type.
:::

```graphql
type GoogleMaps_Component {
  long_name: String
  short_name: String
  types: [String]
}
```

### `long_name`

_String_ - The full text description or name of the address component.

### `short_name`

_String_ - An abbreviated textual name for the address component, if available.

### `types`

_\[String]_ - An array indicating the type of the address component.
