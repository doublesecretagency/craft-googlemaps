# Geolocation Service

These service methods can be accessed like so...

```php
use doublesecretagency\googlemaps\GoogleMapsPlugin;

GoogleMapsPlugin::$plugin->geolocation->...
```

## Public Properties

### `ip`

_string_ - The user's IP address.

## Public Methods

### `geolocateUser()`

Determine user's approximate location via a third-party geolocation service.

#### Returns

_Geolocation_ - A [Geolocation Model](/models/geolocation-model/) representing the approximate location of a visitor.

:::warning
Regardless of which third-party service you are using, **geolocation is an imprecise science**.

A geolocation result will very often point to a user's _internet service provider_, or other internet routing equipment. While that location may be in the general region of the user, do not expect a geolocation result to point directly at a user's real location. 
:::
