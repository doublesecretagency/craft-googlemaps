# Query Parameters

Performing a proximity search is extremely easy, it is simply an extension of a normal Element Query. You can use these query parameters on any element type, wherever you may have assigned your Address field.

In order to trigger the proximity search, there are two additional parameters which need to be executed on a normal [Element Query](https://craftcms.com/docs/3.x/element-queries.html). Here is a simple example...

:::code
```twig
{% set entries = craft.entries
    .myAddressField({
        'target': 'Los Angeles',
        'range': 50
    })
    .orderBy('distance')
    .all()
%}
```
```php
$entries = Entry::find()
    ->myAddressField([
        'target' => 'Los Angeles',
        'range' => 50
    ])
    ->orderBy('distance')
    ->all()
;
```
:::

`myAddressField` is responsible for narrowing your selection of results, returning only the elements which match the proximity search requirements.

`orderBy` ensures that your query is ordered by the `distance` between your search target and the destination. The query results will be sorted from nearest to farthest.

:::warning Use your real field handle!
Each time you see a proximity search in these docs, you'll notice the use of the `myAddressField` placeholder field handle. You will need to replace that with **your actual field handle** in order for it to work as expected.

So if your Address field handle is `businessAddress`, your query might look like this:

```twig
{% set entries = craft.entries
    .businessAddress(options)
    .orderBy('distance')
    .all()
%}
``` 
:::

## `myAddressField(options = [])`

This parameter is responsible for executing the proximity search. The `options` that you specify will further influence how the proximity search is conducted.

See the [Options](/proximity-search/options/) page for a complete list of available options.

## `orderBy('distance')`

`orderBy` is a standard element query parameter, but it must be set to `'distance'` in order to retrieve the results from nearest to farthest.

`distance` is not a real field or column in your database. It is a dynamically created column which allows us to properly sort the results. Additionally, this allows the resulting [Address Models](/models/address-model/) to contain a special pre-populated `distance` value.
