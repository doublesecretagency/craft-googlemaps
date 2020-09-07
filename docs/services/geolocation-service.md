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

### `getVisitor($config = [])`

Determine user's approximate location via a third-party geolocation service. By default, the method will automatically detect the visitor's IP address.

#### Arguments

 - `$config` (_array_) - Optional config settings to override the following:
 
| Option    | Type     | Default               | Description                       |
|-----------|:--------:|:---------------------:|-----------------------------------|
| `ip`      | _string_ | (autodetects)         | The visitor's IP address.         |
| `service` | _string_ | (uses config setting) | Which geolocation service to use. |
 
::: tip SUPPORTED GEOLOCATION SERVICES
At this time, the only accepted values are `"ipstack"`, `"maxmind"`, or _false_. Additional third-party geolocation services may be added in the future.
:::

#### Returns

_Visitor_ - A [Visitor Model](/models/visitor-model/) representing the approximate location of a visitor.

:::warning
Regardless of which third-party service you are using, **geolocation is an imprecise science**.

A geolocation result will very often point to a user's _internet service provider_, or other internet routing equipment. While that location may be in the general region of the user, do not expect a geolocation result to point directly at a user's real location. 
:::
