# Geocoding (Address Lookups)

The concept of **geocoding** is fairly straightforward... it simply means we are using a text string to lookup the exact coordinates of a location. The text string can be as simple or complex as necessary, and Google will do its best to interpret that location as a set of real-world coordinates.

Say you want to take a partial address (ie: _123 Main St_) and determine the precise details of that geographic location. This is commonly referred to as an **address lookup**.

You can perform a geocoding lookup to get a complete address (including latitude & longitude) based on a partial address or postal code. The basic geocoding process can be extremely simple:

:::code
```twig
{% set address = googleMaps.lookup('123 Main St').one() %}
```
```php
$address = GoogleMaps::lookup('123 Main St')->one();
```
:::

There are three available methods (`all`, `one`, `coords`) which determine how the lookup results will be returned to you.

## How it works

Here is a rough diagram of how a geocoding request flows through the plugin... 

<img class="dropshadow" :src="$withBase('/images/geocoding/perform-address-lookup-internal.png')" alt="How it works internally">

### 1. Prepare Lookup

The `GoogleMaps::lookup` method creates a [Lookup Model](/models/lookup-model/), which contains everything the request will need to call the API. There isn't much you can do with a Lookup Model directly, until you append `all()`/`one()`/`coords()` onto the end of it. See the [geocoding target](/geocoding/target/) for more information.

### 2. Specify Results Type

The subsequent [chained method](/geocoding/methods/) determines how you will receive the API results.

 - If you want to get the **complete set of results**, use [`all()`](/models/lookup-model/#all). The results will be an array of [Address Models](/models/address-model/), sorted in order of what Google considers to be the best match.
 - Generally (but not always), it's safe to assume that Google's hunch is correct, and **the first result** is likely to be the correct match. In this case, you may feel comfortable using [`one()`](/models/lookup-model/#one), which returns only a single [Address Model](/models/address-model/).
 - And if all you really need are the **coordinates of the best possible match**, then it's probably safe to use [`coords()`](/models/lookup-model/#coords). This returns a pseudo-model (aka: a glorified array) of [Coordinates](/models/coordinates/).
