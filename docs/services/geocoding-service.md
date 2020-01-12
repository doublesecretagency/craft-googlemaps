# Geocoding Service

These service methods can be accessed like so...

```php
use doublesecretagency\googlemaps\GoogleMapsPlugin;

GoogleMapsPlugin::$plugin->geocoding->...
```

::: warning PARENT CLASS
This service extends the [API Service](/services/api-service).
:::


### `lookup()`

#### Arguments

 - `$parameters` (_string_ or _array_) - Can be either an [**array of parameters**](https://developers.google.com/maps/documentation/geocoding/intro#geocoding), or a **string** representing the geocode target. If the value is provided as a string, it will be interpreted directly as the `address` parameter, and all other parameters will be ignored.

The parameters are based on the [Google Maps Geocoding parameters](https://developers.google.com/maps/documentation/geocoding/intro#geocoding). You can pass through any optional parameters you see there.

This method prepares the lookup API call based on the specified `parameters`. It will compile all of the parameters specified into a Geocoding API endpoint URL. A fully compiled URL will look something like this...

```html
https://maps.googleapis.com/maps/api/geocode/json?address={ADDRESS}&key={KEY}
```

::: warning OMIT THE KEY
You don't need to manually specify the `key` value. It is stored internally, and appended to the API endpoint URL automatically.
:::

**Example using a string:**

```twig
googleMaps.lookup('123 Main St')
```

:arrow_down: ... becomes this... :arrow_down:

```html
https://maps.googleapis.com/maps/api/geocode/json?address=123+Main+St&key={KEY}
```

If you don't need any other parameters, then it's perfectly acceptable to pass in a string directly.

**Example using an array of parameters:**

```twig
googleMaps.lookup({
    'address': '123 Main St',
    'language': 'de',
    'components': 'country:DE'
})
```

:arrow_down: ... becomes this... :arrow_down:

```html
https://maps.googleapis.com/maps/api/geocode/json?address=123+Main+St&language=de&components=country:DE&key={KEY}
```

That API endpoint will be pinged when the `all`/`one`/`coords` method is executed.
