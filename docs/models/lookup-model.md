# Lookup Model

When a Lookup Model is initialized, it should be passed a collection of parameters which specify the lookup details. These parameters will be stored internally once the object is created.

:::code
```twig
{# Configure Lookup Model #}
{% set lookup = GoogleMaps::lookup(parameters) %}

{# Perform the geocoding lookup #}
{% set results = lookup.all() %}
```
```php
// Configure Lookup Model
$lookup = GoogleMaps::lookup($parameters);

// Perform the geocoding lookup
$results = $lookup->all();
```
:::

Once you apply the `all` (or `one`, or `coords`) method, those parameters will be used to ping the [Google Geocoding API](https://developers.google.com/maps/documentation/geocoding/intro#geocoding). The results of each lookup will be cached for 30 days, in order to ease the load on the API.

For more information, see the [Geocoding Methods](/geocoding/methods/) page.

## Public Methods

### `all()`

Perform lookup and return **complete results**. Addresses will be sorted by best match.

#### Returns

`Address[]` - An array of [Address Models](/models/address-model/), or `null` if nothing was found.

### `one()`

Perform lookup and return only the **first matching result**. Generally speaking, the first address is typically the best match.

#### Returns

`Address` - A single [Address Model](/models/address-model/), or `null` if nothing was found.

### `coords()`

Perform lookup and return only the **coordinates** of the **first matching result**.

#### Returns

`coords` - A [coords](/models/coordinates/) object, or `null` if nothing was found.
