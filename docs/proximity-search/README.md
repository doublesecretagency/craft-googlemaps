# Proximity Search

There are many scenarios where you may want to conduct a proximity search...

 - A **store locator** - See which franchise locations are nearest to a customer.
 - A **real estate tool** - Show houses or apartments within a given search area.
 - A **general resource finder** - Display a list of parks, playgrounds, or schools nearby.

Give your users the ability to **find the nearest location**. You can conduct a proximity search to get the distance to each location, and sort them from nearest to farthest.

:::code
```twig
{# Conduct a proximity search #}
{% set entries = craft.entries.myAddressField(options).orderBy('distance').all() %}
```
```php
// Conduct a proximity search
$entries = Entry::find()->myAddressField($options)->orderBy('distance')->all();
```
:::

:::tip TL;DR
Add these two parameters to an otherwise normal Element Query...

```twig
    .myAddressField(options)
    .orderBy('distance')
```

This turns a normal query into a **proximity search**. The two parameters will respectively...

 - Conduct a proximity search with the specified `options`.
 - Sort the results from nearest to farthest.
 
You can specify whatever  `options` are necessary in order to further configure the proximity search. To see what's possible, check out the [full list of options...](/proximity-search/options/)
:::

## Simple Example

### Find all locations within 50 miles of Seattle

:::code
```twig
{# example.com/search?near=Seattle #}

{# Get proximity search target from user query #}
{% set target = craft.app.request.getParam('near') ?? null %}

{# Configure the proximity search #}
{% set options = {
    'target': target,
    'range': 50
} %}

{# Run the proximity search #}
{% set entries = craft.entries
    .section('locations')
    .myAddressField(options)
    .orderBy('distance')
    .all() %}
```
```php
// example.com/search?near=Seattle

// Get proximity search target from user query
$target = Craft::$app->request->getParam('near') ?? null;

// Configure the proximity search
$options = [
    'target' => $target,
    'range' => 50
];

// Run the proximity search
$entries = Entry::find()
    ->section('locations')
    ->myAddressField($options)
    ->orderBy('distance')
    ->all();
```
:::

Just as in a typical element query, the results will be an **array of Entries**. However, it will _also_ be sorted by distance from the specified target. In this particular example, locations over 50 miles away will be excluded from the results. 

## Query Parameters

As you've already noticed, there are two special query parameters which are responsible for turning the element query into a **proximity search**. To see the complete details of how each parameter works, check out the [Query Parameters](/proximity-search/query-parameters/) page.

### myAddressField(options)

 - In order to conduct a proximity search, specify your Address **field handle** with whatever `options` are appropriate. Check out the full [list of options...](/proximity-search/options/)

### orderBy('distance')

 - You will almost certainly want to sort your query by **closest matches**. Adding this parameter will ensure that the results are sorted from nearest to farthest.

::: warning Sorting by Distance
The `distance` "column" is not a real column in the database. It is dynamically generated on-the-fly, and the values are then accessible via the resulting [Address Models](/models/address-model).
:::

## The `distance` Property

When conducting a proximity search, you will end up with a normal array of elements. And as usual, the value of each Address field will be available as an **Address Model**. However, in the case of a proximity search, each Address Model will also contain a special extra property... [distance](/models/address-model/#distance).

The `distance` property of each Address Model will contain the relative distance to the specified search target. It is automatically populated if the element was retrieved as part of a proximity search. If the element was _not_ retrieved as part of a proximity search, then `distance` will return a _null_ value instead.

## Multiple Address fields

If your entries contain multiple Address fields, it doesn't matter which handle you use in order to trigger the proximity search. Each Address field is capable of triggering the exact same proximity search.

By default, the proximity search will include _all_ Address fields. You can specify a proximity search against a _specific_ Address field by specifying the [`field` option](/proximity-search/options/#fields).
