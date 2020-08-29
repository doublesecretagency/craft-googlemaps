# Region Biasing


[Component Filtering](https://developers.google.com/maps/documentation/geocoding/overview#component-filtering)



## What is region biasing?

Sometimes your [proximity search](/proximity-search/) lookups don't match the expected target. Instead, the lookup may be matching a different, but similarly named, geographic location. This is often because the specified target shares a similar name as a more well-known area.

**FOR EXAMPLE:** You have visitors searching for "Venice" (the city in California). But there's a more famous Venice in the world (the city in Italy), and therefore the Italian city takes precedence.

You can specify which region to hone in on by using the `components` option...

```twig
{% set options = {
    'target': {
        'address': 'Venice'
        'components': {
            'country': 'US',
            'administrative_area': 'California',
        },
    },
} %}
```

## How it works

When conducting a proximity search, there are several formats you can use for the `target` value. However, if you pass a **string**, it will trigger an [Address Lookup](/geocoding/) to determine the starting coordinates of the proximity search. There are two [options](/proximity-search/options/) which affect the Address Lookup.

| Option       | Type                 | Description                                     |
|--------------|:--------------------:|-------------------------------------------------|
| `target`     | _string_             | Center point for the proximity search.          |
| `components` | _object_ or _string_ | An associative array of Google Maps components. |

::: tip AVAILABLE COMPONENT FILTERS
The following [filters](https://developers.google.com/maps/documentation/geocoding/intro#ComponentFiltering) can be specified in the `components` object: 

 - `route`
 - `locality`
 - `administrative_area`
 - `postal_code`
 - `country`
:::

## Formatting options

There are two ways to pass `components` data. The first method is to use an **object**, as demonstrated in the example above.

The second method is to pass the entire `components` value as a **string**. This is possible because the underlying API specifications accepts a string format.

**From the Geocoding API docs:**

>A filter consists of a list of `component:value` pairs separated by a pipe (`|`).

It does not matter whether you pass in a **string** or **object** to `components`. Both of these examples will do the exact same thing...

```twig
{% set options = {
    target: 'Venice',
    components: {
        country: 'US',
        administrative_area: 'California',
    },
} %}
```

```twig
{% set options = {
    target: 'Venice',
    components: 'country:US|administrative_area:California',
} %}
```

If `components` is a string, the plugin will not parse it down any further before handing it off to the Google Maps Geocoding API.

For more information, see the official Google docs on [Component Filtering...](https://developers.google.com/maps/documentation/geocoding/intro#ComponentFiltering)
