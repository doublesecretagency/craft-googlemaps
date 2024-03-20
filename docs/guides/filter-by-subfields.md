---
description:
---

# Filter by Subfields

When conducting a proximity search, you can use the [`subfields` option](/proximity-search/options/#subfields) to exclude results which don't **exactly match** the specified subfields. It allows you to filter the proximity search results based on existing subfield values of the Address field.

To only include results in the United States, your code may look something like this...

:::code
```twig
{% set options = {
    'subfields': {
        'country': 'United States'
    }
} %}
```
```php
$options = [
    'subfields' => [
        'country' => 'United States'
    ]
];
```
:::

Here's a more complex example, using a dynamically specified city...

:::code
```twig
{# Get specified target city #}
{% set city = craft.app.request.getParam('city') ?? null %}

{# Only include results in the matching city #}
{% set options = {
    'subfields': {
        'city': city
    }
} %}

{# Run the proximity search #}
{% set entries = craft.entries
    .section('locations')
    .myAddressField(options)
    .all() %}
```
```php
// Get specified target city
$city = Craft::$app->request->getParam('city') ?? null;

// Only include results in the matching city
$options = [
    'subfields' => [
        'city' => $city
    ]
];

// Run the proximity search
$entries = Entry::find()
    ->section('locations')
    ->myAddressField($options)
    ->all();
```
:::

You can filter by any of the following [Address Model](/models/address-model/) subfield values:

- `name`
- `street1`
- `street2`
- `city`
- `state`
- `zip`
- `neighborhood`
- `county`
- `country`
- `countryCode`
- `placeId`
- `lat`
- `lng`

The value of each subfield filter could be either:

- A **string**, if there is only one acceptable value for the subfield.
- An **array**, if there are multiple acceptable values for the subfield.

## Filter by String

You can specify each subfield filter as a collection of `subfield: value` pairs. It's possible to specify more than one subfield, in which case _every specified subfield_ will require an exact match.

:::code
```twig
{# If the city is Los Angeles AND the state is CA #}
{% set options = {
    'subfields': {
        'city': 'Los Angeles',
        'state': 'CA'
    }
} %}
```
```php
// If the city is Los Angeles AND the state is CA
$options = [
    'subfields' => [
        'city' => 'Los Angeles',
        'state' => 'CA'
    ]
];
```
:::

## Filter by Array

Each subfield filter can also be specified as an array, which allows for multiple valid matches:

:::code
```twig
{# If the country is United Kingdom OR Germany OR France #}
{% set options = {
    'subfields': {
        'country': ['United Kingdom', 'Germany', 'France']
    }
} %}
```
```php
// If the country is United Kingdom OR Germany OR France
$options = [
    'subfields' => [
        'country' => ['United Kingdom', 'Germany', 'France']
    ]
];
```
:::

## Subfield Filter Fallback

When a user searches for a partial address, we use the specified information to ping the Google API and determine their **official geographic coordinates**. Google performs an address lookup (also known as [geocoding](/geocoding/)) in order to determine the _center point_ of the user's specified area.

However, this can be problematic if the user has only entered a _broad area_ instead of a narrow location. If the user has only entered a postal code or city name, Google will return coordinates for the _geographic center_ of that region. This can lead to some confusion, as the "closest" results may belong in a neighboring state or province (due to the oddly-shaped nature of most regions).

To solve this problem, you can use the **subfield filter fallback** mechanism. Simply set the `subfields` value to `"fallback"` to have it behave as an automatic fallback for an otherwise normal proximity search.

:::code
```twig
{% set options = {
    'target': 'California',
    'subfields': 'fallback'
} %}
```
```php
$options = [
    'target' => 'California',
    'subfields' => 'fallback'
];
```
:::

:::warning What triggers the fallback?
The `target` will always be sent to the Google API normally to conduct the address lookup, regardless of whether the fallback gets triggered. Before using the API results, the fallback mechanism checks the first item. If it describes a broad geographic area (ie: state/province or country), the fallback filter will be applied.
:::
