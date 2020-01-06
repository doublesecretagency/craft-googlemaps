# Geolocation Model

The properties and methods of the Geolocation Model are identical whether you are accessing them via Twig or PHP.

```twig
{% set visitor = googleMaps.getGeolocation() %}
```

```php
$visitor = GoogleMaps::getGeolocation(); // Which is an alias for...
$visitor = GoogleMaps::$plugin->geolocation->ipLookup($ip = null);
```

::: warning ADDITIONAL PROPERTIES AND METHODS
The Geolocation Model is an extension of the [Location Model](/models/location-model/). It contains all properties and methods of the Location Model, plus the properties and methods shown below.

You can access `lat` and `lng` just as easily as `ip` or `service`.
:::

## Public Properties

### `ip`

_string_ - The IP address of the geolocation lookup.

### `service`

_string_ - The name of the service used to perform the geolocation.

### `country`

_string_ - The country determined by the geolocation lookup.


## Public Methods

### `geolocate()`
