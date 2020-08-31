# Region Biasing

Sometimes your [proximity search](/proximity-search/) lookups don't match the expected target. Instead, the lookup may be matching a different, but similarly named, geographic location. This is often because the specified target shares a similar name as a more well-known area.

:::tip Use an array of parameters
When using Region Biasing, you must specify the [target](/proximity-search/options/#target) option as a **set of parameters** which will be passed into an [address lookup](/geocoding/parameters/#using-an-array-of-parameters) internally.
:::

## A real-world example

Say you have visitors searching for "Venice" (the city in California). But there happens to be a more famous Venice in the world (the city in Italy). Therefore, the Italian city takes precedence.
 
In this case, we need to tell Google which region to focus on. We can do that by adding a `components` value to the `target` option...

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

## How it works

Internally, the plugin does a [geocoding](/geocoding/) call to determine the center point of the proximity search. It begins by calling the `GoogleMaps::lookup` method, and passes along whatever was specified as the `target` value.

<img class="dropshadow" :src="$withBase('/images/guides/region-biasing.png')" alt="Diagram of Component Filtering" style="max-width:640px">

It's up to you as to which `components` should be specified. We recommend using the minimal restrictions needed in order to achieve the intended results.

:::warning Available Filters
The following filters can be specified in the `components` object: 

 - `route`
 - `locality`
 - `administrative_area`
 - `postal_code`
 - `country`
:::

You can read more about the acceptable filter values on the [Google documentation](https://developers.google.com/maps/documentation/geocoding/overview#component-filtering).

## Formatting options

This is just a normal Geocoding request, although it's important to note that we are passing **an array of parameters** into the `lookup` method (instead of a simple string address). There are two ways to specify the `components` data.

The first approach is to use an **associative array of Google Maps components**.

:::code
```twig
{% set options = {
    'target': 'Venice',
    'components': {
        'country': 'US',
        'administrative_area': 'California',
    }
} %}
```
```php
$options = [
    'target' => 'Venice',
    'components' => [
        'country' => 'US',
        'administrative_area' => 'California',
    ]
];
```
:::

The second approach is to pass the entire `components` value as a **string**. This is possible because the underlying API specifications accept a string format.

:::code
```twig
{% set options = {
    'target': 'Venice',
    'components': 'country:US|administrative_area:California'
} %}
```
```php
$options = [
    'target' => 'Venice',
    'components' => 'country:US|administrative_area:California'
];
```
:::

**From the Geocoding API docs:**

>A filter consists of a list of `component:value` pairs separated by a pipe (`|`).

It makes no difference whether the `components` are specified as a **string** or **array**. Both formats will accomplish the same thing. If `components` is already a string, the plugin will not parse it down any further before handing it off to the Google Maps Geocoding API.

For more information, see the official Google docs on [Component Filtering...](https://developers.google.com/maps/documentation/geocoding/overview#component-filtering)
