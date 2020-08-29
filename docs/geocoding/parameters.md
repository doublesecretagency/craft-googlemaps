# Geocoding Parameters

When generating a [Lookup Model](/models/lookup-model/), you only need to pass in a single parameter. However, that parameter can either be _string_ or an _array_, depending on whether or not you need granular control over the API call. 

## Using a simple string

This is the simplest, and most straightforward approach. The specified string will be sent to the Google API as the `address` parameter, with no other requirements.

For the vast majority of cases, you won't need to pass in anything more complex than a simple target string.

```twig
{# Lookup based on simple string #}
{% set results = googleMaps.lookup('123 Main St').all() %}

{# Generates the following API URL: #}
https://maps.googleapis.com/maps/api/geocode/json?address=123+Main+St&key={KEY}
```

## Using an array of parameters

If your lookup needs are more complex, you can pass in any values [allowed by the Google API](https://developers.google.com/maps/documentation/geocoding/overview#geocoding-lookup).

```twig
{# Lookup based on a complex array of requirements #}
{% set results = googleMaps.lookup({
   'address': '123 Main St',
   'language': 'de',
   'components': 'country:DE'
}).all() %}

{# Generates the following API URL: #}
https://maps.googleapis.com/maps/api/geocode/json?address=123+Main+St&language=de&components=country:DE&key={KEY}
```

::: warning Do not specify the API key here
When adding requirements for your `lookup`, you do not need to manually specify the `key` value. It is stored internally, and will be appended to the API endpoint URL automatically.
:::
