# Filter by Subfields

When conducting a proximity search, the [`subfields` option](/proximity-search/options/#subfields) will exclude results which don't _exactly match_ the specified subfields. It allows you to filter the proximity search results based on existing subfield values of the Address field.

:::code
```twig
{% set options = {
    'subfields': {
        'state': 'CO',
        'country': 'United States'
    }
} %}
```
```php
$options = [
    'subfields' => [
        'state' => 'CO',
        'country' => 'United States'
    ]
];
```
:::

The value of each filter could be either:

 - A **string**, if there is only one acceptable value for the subfield.
 - An **array**, if there are multiple acceptable values for the subfield.

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

You can filter by any of the following [Address Model](/models/address-model/) subfield values:
 
 - `street1`
 - `street2`
 - `city`
 - `state`
 - `zip`
 - `country`
 - `lat`
 - `lng`

## Examples

#### Get all companies whose business address is in Colorado (sorted alphabetically)...

:::code
```twig
{# Must be in Colorado #}
{% set options = {
    'subfields': {
        'state': 'CO',
        'country': 'United States'
    }
} %}

{# Ordered by title (not distance) #}
{% set entries = craft.entries
    .section('companies')
    .businessAddress(options)
    .orderBy('title')
    .all() %}
```
```php
// Must be in Colorado
$options = [
    'subfields' => [
        'state' => 'CO',
        'country' => 'United States'
    ]
];

// Ordered by title (not distance)
$entries = Entry::find()
    ->section('companies')
    ->businessAddress($options)
    ->orderBy('title')
    ->all();
```
:::

#### Get the [visitor's current location](/geolocation/), then find the closest bars in Los Angeles...

:::code
```twig
{# Get visitor info via geolocation #}
{% set visitor = googleMaps.visitor %}

{# Search near the visitor, but only in Los Angeles #}
{% set options = {
    'target': visitor.getCoords(),
    'subfields': {
        'city': 'Los Angeles',
        'state': 'CA',
    }
} %}

{# Get closest bars in Los Angeles #}
{% set locations = craft.entries
    .section('locations')
    .barAddress(options)
    .orderBy('distance')
    .all() %}
```
```php
// Get visitor info via geolocation
$visitor = GoogleMaps::getVisitor();

// Search near the visitor, but only in Los Angeles
$options = [
    'target' => $visitor->getCoords(),
    'subfields' => [
        'city' => 'Los Angeles',
        'state' => 'CA',
    ]
];

// Get closest bars in Los Angeles
$locations = Entry::find()
    ->section('locations')
    ->barAddress($options)
    ->orderBy('distance')
    ->all();
```
:::
