# Geocoding Parameters

When generating a [Lookup Model](/models/lookup-model/), you only need to pass in a single parameter. However, that parameter can either be _string_ or an _array_, depending on whether or not you need granular control over the API call. 

## Using a simple string

This is the simplest, and most straightforward approach. The specified string will be sent to the Google API as the `address` parameter, with no other requirements.

For the vast majority of cases, you won't need to pass in anything more complex than a simple target string.

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

::: warning [KEY] will be appended automatically
The `key` value will be automatically appended, you do not need to specify it here.
:::

## Using an array of parameters

The most common reason that you would want to use an array of parameters is for the purpose of [Region Biasing](/guides/region-biasing/#formatting-options). This can make a big difference if you feel like the proximity search isn't focusing on the right part of the world.

By specifying an array of parameters, you are able to _directly control_ which URL parameters will be sent to the Google API during the geocoding lookup call. There are many reasons you may want to do that, including controlling the language of the results.

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

::: warning Do not specify the API key here
When adding requirements, you do not need to manually specify the `key` value. The plugin stores it internally, and it will be appended to the API endpoint URL automatically.
:::

If your lookup needs are more complex, you can pass in any values [allowed by the Google API](https://developers.google.com/maps/documentation/geocoding/overview#geocoding-lookup).
