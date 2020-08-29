# Filter by Fields and Subfields

You can filter the results of a proximity search by taking advantage of the `fields` and `subfields` [options](/proximity-search/options/). While they sound nearly identical, they do slightly different things.

## `fields`

The `fields` option will restrain the proximity search to only the Address field(s) specified here. If you omit this option, it will conduct the proximity search across _all_ fields in that section by default.

:::code
```twig
{% set options = {
    'fields': 'businessAddress'
} %}
```
```php
$options = [
    'fields' => 'businessAddress'
];
```
:::

The value of `fields` can be either:

 - _string_ - The field handle of the Address field you want included in the proximity search.
 - _array_ - An array of Address field handles to include in the proximity search.

## `subfields`

The `subfields` option will exclude results which don't _exactly match_ the specified subfields. It allows you to filter the proximity search results based on existing subfield values of the Address field.

:::code
```twig
{% set options = {
    'subfields': {
        'state': 'CO',
        'country': 'United States',
    }
} %}
```
```php
$options = [
    'subfields' => [
        'state' => 'CO',
        'country' => 'United States',
    ]
];
```
:::

Your subfield options will largely align with the [Address Model](/models/address-model/). Specifically, you can filter by the following: `street1`, `street2`, `city`, `state`, `zip`, `country`, `lat`, `lng`

Much like the value of `fields`, the value of `subfields` can be either a _string_ or _array_.

 - _string_ - The only acceptable value of the subfield.
 - _array_ - An array of acceptable values of the subfield.

## Examples

**Include only search results from the specified countries:**

:::code
```twig
{% set options = {
    'subfields': {
        'country': ['United Kingdom', 'Germany', 'France']
    }
} %}
```
```php
$options = [
    'subfields' => [
        'country' => ['United Kingdom', 'Germany', 'France']
    ]
];
```
:::

**Get all users whose business address is in Colorado (sorted alphabetically):**

:::code
```twig
{% set options = {
    'fields': 'businessAddress',
    'subfields': {
        'state': 'CO',
        'country': 'United States',
    }
} %}

{% set users = craft.users
    .businessAddress(options)
    .orderBy('title')
    .all() %}
```
```php
$options = [
    'fields' => 'businessAddress',
    'subfields' => [
        'state' => 'CO',
        'country' => 'United States',
    ]
];

$users = User::find()
    ->businessAddress($options)
    ->orderBy('title')
    ->all();
```
:::

**Get the [visitor's current location](/geolocation/), then find the closest bars & casinos in Las Vegas:**

:::code
```twig
{% set visitor = googleMaps.getVisitor() %}

{% set options = {
    'target': visitor.getCoords(),
    'fields': ['barAddress', 'casinoAddress'],
    'subfields': {
        'city': 'Las Vegas',
        'state': 'NV',
    }
} %}

{% set locations = craft.entries
    .section('locations')
    .barAddress(options)
    .orderBy('distance')
    .all() %}
```
```php
$visitor = GoogleMaps::getVisitor();

$options = [
    'target' => $visitor->getCoords(),
    'fields' => ['barAddress', 'casinoAddress'],
    'subfields' => [
        'city' => 'Las Vegas',
        'state' => 'NV',
    ]
];

$locations = Entry::find()
    ->section('locations')
    ->barAddress($options)
    ->orderBy('distance')
    ->all();
```
:::
