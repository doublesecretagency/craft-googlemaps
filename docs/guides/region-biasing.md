# Region Biasing

Sometimes your [proximity search](/proximity-search/) lookups don't match the expected target. Instead, the lookup may be matching a different, but similarly named, geographic location. This is often because the specified target shares a similar name as a more well-known area.

**FOR EXAMPLE:** You have visitors searching for "Venice" (the city in California). But there's a more famous Venice in the world (the city in Italy), and therefore the Italian city takes precedence.

Within the `target` option, you can specify which region to focus on by specifying the necessary `components`...

:::code
```twig
{# Configure the proximity search #}
{% set options = {
    'target': {
        'address': 'Venice',
        'components': {
            'country': 'US',
            'administrative_area': 'California',
        },
    },
} %}

{# Run the proximity search #}
{% set entries = craft.entries
    .section('locations')
    .myAddressField(options)
    .orderBy('distance')
    .all() %}
```
```php
// Configure the proximity search
$options = [
    'target' => [
        'address' => 'Venice',
        'components' => [
            'country' => 'US',
            'administrative_area' => 'California',
        ],
    ],
];

// Run the proximity search
$entries = Entry::find()
    ->section('locations')
    ->myAddressField($options)
    ->orderBy('distance')
    ->all();
```
:::

Internally, the plugin does a [geocoding call](/geocoding/) to determine the center point of the proximity search. It starts by calling the `GoogleMaps::lookup` method, passing it whatever you have specified for `target`. For complete details on what the `lookup` method accepts, take a look at the [Geocoding Parameters](/geocoding/parameters/#using-an-array-of-parameters) page.

The `components` branch then gets passed along even further. When the lookup pings the Google Geocoding API, it sends along whatever is specified for the `components`. You can read about the acceptable values on the [Google documentation](https://developers.google.com/maps/documentation/geocoding/overview#component-filtering).

<img class="dropshadow" :src="$withBase('/images/guides/region-biasing.png')" alt="Diagram of Component Filtering">

## How it works

When conducting a proximity search, you can 

When conducting a proximity search, there are several formats you can use for the `target` value. However, if you pass a **string**, it will trigger an [Address Lookup](/geocoding/) to determine the starting coordinates of the proximity search. There are two [options](/proximity-search/options/) which affect the Address Lookup.

| Option       | Type                 | Description                                     |
|--------------|:--------------------:|-------------------------------------------------|
| `target`     | _string_             | Center point for the proximity search.          |
| `components` | _object_ or _string_ | An associative array of Google Maps components. |

::: tip AVAILABLE COMPONENT FILTERS
The following [filters](https://developers.google.com/maps/documentation/geocoding/overview#component-filtering) can be specified in the `components` object: 

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

For more information, see the official Google docs on [Component Filtering...](https://developers.google.com/maps/documentation/geocoding/overview#component-filtering)
