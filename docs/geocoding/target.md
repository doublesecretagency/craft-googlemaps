---
description: The geocoding target can be a string or an array, depending on the needs of your search. Configure the lookup with an array for more complex searches.
---

# Geocoding Target

When creating a [Lookup Model](/models/lookup-model/), you only need to pass a single **string** or **array** value...

### `lookup(target)`

#### Arguments

- `target` (_string_ or _array_) - Either a [simple string](#using-a-simple-string) or an [array of parameters](#using-an-array-of-parameters).

## Using a simple string

This is the simplest, and most straightforward approach. The specified string will be passed directly to the Google API as the `address` parameter with no other parameters specified.

For the vast majority of cases, a simple target string is all you need...

:::code
```twig
{# Lookup based on a simple string #}
{% set results = googleMaps.lookup('123 Main St').all() %}
```
```php
// Lookup based on a simple string
$results = GoogleMaps::lookup('123 Main St')->all();
```
:::

The example above will compile and ping the following Google API URL:

```
https://maps.googleapis.com/maps/api/geocode/json?address=123+Main+St&key=[KEY]
```

:::warning [KEY] added automatically
You do not need to specify the `key` value, it will be automatically appended here.
:::

## Using an array of parameters

You generally will not need to specify anything more complicated than a basic string. However, if your lookup needs are more complex, you can pass in an array of values [allowed by the Google API](https://developers.google.com/maps/documentation/geocoding/overview#geocoding-lookup).

Here are a few reasons why you may want to specify an array of Google-friendly values...

### Region Biasing

The most common reason why you might want to use an array of parameters would be for the purpose of [region biasing](/guides/region-biasing/). This can make a big difference if you feel like the proximity search isn't focusing on the right part of the world.

:::tip Proximity Search
Internally, the [proximity search](/proximity-search/) mechanism relies heavily on the geocoding mechanism.
:::

### Language Control

When you specify an array of parameters, you are directly controlling which URL parameters get sent to the Google API. This allows you to control the language of the results.

:::code
```twig
{# Lookup based on a complex array of requirements #}
{% set results = googleMaps.lookup({
   'address': '123 Main St',
   'language': 'de',
   'components': 'country:DE'
}).all() %}
```
```php
// Lookup based on a complex array of requirements
$results = GoogleMaps::lookup([
   'address' => '123 Main St',
   'language' => 'de',
   'components' => 'country:DE'
])->all();
```
:::

The example above will compile and ping the following Google API URL:

```
https://maps.googleapis.com/maps/api/geocode/json
    ?address=123+Main+St
    &language=de
    &components=country:DE
    &key=[KEY]
```

:::warning Do not specify the API key here
When adding requirements, you do not need to manually specify the `key` value. The plugin stores it internally, and it will be appended to the API endpoint URL automatically.
:::
