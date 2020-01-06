# Proximity Search in Twig

Here is a general example...

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

Query your entries (or any element type) as you normally would. Chain the following values into your query...

## `.myAddressField(params = [])`

`params` is an array of options to pass in which will convert this query to a _proximity search_. You can see the full list of options here...

::: tip NOTE

In the example above, `myAddressField` is simply a placeholder for **your field handle**.

:::

## `.orderBy('distance')`

The `distance` value is not a field in your database. It is a dynamically created column, with which we are able to sort the results from nearest to farthest. The resulting array of entries will include that dynamic `distance` value (see more on the [Address Model](/address-field/address-model/#distance)) 
