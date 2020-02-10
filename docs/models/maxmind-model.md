# Maxmind Model

The Maxmind Model interacts with the [MaxMind geolocation service](https://www.maxmind.com/). You can subscribe to MaxMind and use their services to perform geolocation lookups on your visitors.

::: warning FOR EDGE CASES ONLY
You will rarely need to call this model directly. If you have MaxMind configured as your default geolocation service, then the preferred method is to perform geolocation normally.

For more info, check out the guide on [Complex Geolocation...](/guides/complex-geolocation/)
:::

## Public Methods

### `geolocate()`

```php
use doublesecretagency\googlemaps\models\Maxmind;

$visitor = Maxmind::geolocate($ipAddress);
```

#### Arguments

 - `$ip` (_string_) - An IP address on which to perform the geolocation.
 - `$parameters` (_array_) - An optional configuration array.

#### Returns

_Visitor_ - A [Visitor Model](/models/visitor-model/) containing the approximate location of a visitor.
