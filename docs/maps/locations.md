# Locations

There are several methods of the [Dynamic Map Model](/models/dynamic-map-model/) and [Static Map Model](/models/static-map-model/) which have a `locations` parameter.

What are "locations"? Glad you asked.

## Valid types of locations

### Coordinates

:::warning LD NOTE
Are coordinates fair game? It will be required in JS, should it be allowed in PHP/Twig also?
:::

### Address Models

The `locations` value can be set as an individual [Address Model](/models/address-model/), or as an array of Address Models.

```twig
{# Just one Address Model #}
{% set locations = entry.myAddressField %}

{# An array of Address Models #}
{% set locations = [
    entry.homeAddress,
    entry.businessAddress
] %}
```

### Elements

The `locations` value can be set as an individual [Element](https://craftcms.com/docs/3.x/elements.html), or as an array of Elements. This can include any native Element Types (ie: Entries, Categories, Users, etc), as well as any custom Element Types introduced by other plugins or modules.

```twig
{# Just one Element #}
{% set locations = entry %}

{# An array of Elements #}
{% set locations = craft.entries.all() %}
```

:::tip Elements must have a valid Address field
When using an Element, ensure that it has at least one Address field attached to it. The plugin will automatically render all valid addresses on the map.
:::
