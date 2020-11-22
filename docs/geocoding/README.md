# Geocoding (Address Lookups)

The concept of **geocoding** is fairly straightforward... For example, say you want to take a partial address ("123 Main St") and determine the precise details of that geographic location. This is commonly referred to as an "address lookup".

You can perform a geocoding lookup to get a complete address (including latitude & longitude) based on a partial address or postal code. The basic geocoding process can be extremely simple:

:::code
```twig
{% set address = googleMaps.lookup('123 Main St').one() %}
```
```php
$address = GoogleMaps::lookup('123 Main St')->one();
```
:::

There are three `lookup` methods that can be performed (`all`, `one`, `coords`). To see the complete details of each method, see the [Lookup Model](/models/lookup-model/) documentation for more information.

Internally, this plugin uses the `lookup` methods quite frequently under the hood. The examples above use the [`GoogleMaps` Helper Class](/helper/), which is acting as a wrapper for the [Geocoding Service](/services/geocoding-service/).

Here is a rough diagram of how a geocoding request flows through the plugin... 

<img class="dropshadow" :src="$withBase('/images/geocoding/perform-address-lookup-internal.png')" alt="How it works internally">

The `Geocoding::lookup` method will create a [Lookup Model](/models/lookup-model/), which contains everything the geocoding request needs to call the Google Geocoding API. The model also has three classes, which determine how you will receive the API results.

 - If you want to get the complete set of results from Google, you would use the [`all()`](/models/lookup-model/#all) method. The results will be an array of [Address Models](/models/address-model/), sorted in order of what Google considers to be the best match.
 - Generally (but not always), it's safe to assume that Google's hunch is correct, and the _first_ result is likely to be the correct match. In this case, you may feel comfortable using the [`one()`](/models/lookup-model/#one) method. This will return only a single [Address Model](/models/address-model/).
 - And if all you really need are the _coordinates_ of the _best possible match_, then it's probably safe to use the [`coords()`](/models/lookup-model/#coords) method. This returns a pseudo-model (aka: a glorified array) of [Coordinates](/models/coordinates/).

The `GoogleMaps::lookup` method shown above is actually just a _wrapper_ for the `Geocoding::lookup` service method. They are functionally identical. To learn more about why this wrapper exists, read about the [Helper Class](/helper/).

## Geocoding Service

The lookup behavior is handled by the [Geocoding Service](/services/geocoding-service/), specifically the `lookup` method. It creates and returns a [Lookup Model](/models/lookup-model/) in preparation for a Google API request.

There are multiple ways to access the `lookup` service method, each of them are equally valid. For more information on the available methods, take a look at the [Geocoding Methods](/geocoding/methods/) documentation.

Although it is preferable to rely on the helper method, it is also possible to use the underlying service method directly. Here is a side-by-side view of both approaches...

:::code
```php via Helper
use doublesecretagency\googlemaps\helpers\GoogleMaps;

$lookupModel = GoogleMaps::lookup('123 Main St');
```
```php via Service
use doublesecretagency\googlemaps\GoogleMapsPlugin;

$lookupModel = GoogleMapsPlugin::$plugin->geocoding->lookup('123 Main St');
```
:::

:::warning Use the Helper Class (if possible)
While those are both valid approaches (and will return the exact same result), it is preferred to use the [`GoogleMaps` Helper Class](/helper/) whenever possible. This promotes consistency between PHP and Twig, and sets a common practice for other developers to follow.
:::

Both of these `lookup` approaches will return the same thing: a [Lookup Model](/models/lookup-model/). There isn't much you can do with a Lookup Model directly, until you append `.all()` (or `.one()`, or `.coords()`) onto the end of it.

## How the `lookup` method works

This service method prepares a lookup API call based on the specified `parameters`. It creates a [Lookup Model](/models/lookup-model/) to compile all of the parameters specified into a Google Geocoding API endpoint URL. A fully compiled URL will look something like this...

```html
https://maps.googleapis.com/maps/api/geocode/json?address={ADDRESS}&key={KEY}
```

The fully-compiled endpoint will be pinged as soon as the `all` (or `one`, or `coords`) method is executed.
