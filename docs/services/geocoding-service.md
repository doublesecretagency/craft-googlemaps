# Geocoding Service

These service methods can be accessed like so...

```php
use doublesecretagency\googlemaps\GoogleMapsPlugin;

GoogleMapsPlugin::$plugin->geocoding->...
```

:::warning PARENT CLASS
This service extends the [API Service](/services/api-service).
:::

### `lookup()`

Generates a [Lookup Model](/models/lookup-model/) based on a given set of parameters.

#### Arguments

 - `$parameters` (_string_ or _array_) - Can be either an [**array of parameters**](https://developers.google.com/maps/documentation/geocoding/overview#geocoding-lookup), or a **string** representing the geocode target. If the value is provided as a string, it will be interpreted directly as the `address` parameter, and all other parameters will be ignored.
 
The parameters are based on the [Google Geocoding parameters](https://developers.google.com/maps/documentation/geocoding/overview#geocoding-lookup). You can pass through any optional parameters you see there.

:::warning HOW IT WORKS
Take a peek under the hood, read about [how the `lookup` method works...](/geocoding/)
:::
