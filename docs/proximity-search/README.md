# Proximity Search

Give your users the ability to **find the nearest location**. You can conduct a proximity search to get the distance to each location, and sort them by nearest to farthest.

::: warning TL;DR
Add these two parameters to an otherwise normal Element Query...

```twig
.myAddressField(options).orderBy('distance')
```

This will tell Craft to:

 - Conduct a proximity search with the options you specified.
 - Sort the results by closest distance to your specified target.

The `options` parameter is where you can configure the proximity search. To see what's possible, check out the [full list of options](/proximity-search/options/) which describes everything that can be configured.
:::

Here is a straightforward practical example...

**Find locations within 50 miles of Seattle:**

```twig
{# example.com/search?near=Seattle #}

{# Get proximity search target from user query #}
{% set target = craft.app.request.getParam('near') ?? null %}

{# Configure the proximity search #}
{% set options = {
    target: target,
    range: 50,
} %}

{# Run the proximity search #}
{% set entries = craft.entries
    .section('locations')
    .myAddressField(options)
    .orderBy('distance')
    .all()
%}
```

The value of `entries` will be set to **an array of Entries within the specified range**. The results will be sorted by **closest to the target**.

The Address field of each Entry will be populated with an [Address Model](/models/address-model/). You will be able to access the [`distance`](/models/address-model/#distance) value of each Address, which represents how far it is from the proximity search target.

## .myAddressField(options)

In order to conduct a proximity search, specify your Address **field handle** with whatever `options` are appropriate.

Check out the full [list of options...](/proximity-search/options/)

::: warning PLACEHOLDER
Please **do not** copy the exact parameter "myAddressField". That is simply a placeholder, since your actual field handle could be anything. When you see "myAddressField" used around these docs, always replace it with your **actual Address field handle**.
:::

::: tip ANY ADDRESS FIELD
When conducting the proximity search, it doesn't really matter which Address field you use to trigger the search. By default, the proximity search will include _all_ Address fields.

For example, if you had two Address fields with the handles of `homeAddress` and `businessAddress`, these two lines would do exactly the same thing:

```twig
.homeAddress(options).orderBy('distance')
```

```twig
.businessAddress(options).orderBy('distance')
```
:::

## .orderBy('distance')

You will almost certainly want to sort your query by **closest matches**. Adding this parameter will ensure that the results are sorted from nearest to farthest.

::: warning SORTING BY DISTANCE
The `distance` "column" is not a real column in the database. It is dynamically generated on-the-fly, and then the values are accessible via the resulting [Address Models](/models/address-model/#distance).
:::
